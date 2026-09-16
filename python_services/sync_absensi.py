import os
import sys
import tempfile
import subprocess
import urllib.request
import csv
import io
import pymysql
from dotenv import load_dotenv
from datetime import datetime

# Konfigurasi
URL_MDB = "http://10.147.17.20/att2000.mdb"
ENV_PATH = "/var/www/gkr_myid/.env"
LOG_FILE = "/var/www/gkr_myid/.agents/absensi/absen_sync.log"

def write_log(msg):
    timestamp = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    log_msg = f"[{timestamp}] {msg}\n"
    print(log_msg.strip())
    try:
        with open(LOG_FILE, "a") as f:
            f.write(log_msg)
    except Exception:
        pass

def load_db_config():
    load_dotenv(ENV_PATH)
    return {
        'host': 'localhost',
        'user': 'root', # Hardcoded based on implementation plan or use env
        'password': '102013', # Hardcoded based on implementation plan
        'database': 'gkr_myid',
        'cursorclass': pymysql.cursors.DictCursor
    }

def fetch_and_sync():
    write_log("MEMULAI: Proses sinkronisasi absensi (Data Tabel) dijalankan.")
    
    # Buat file sementara yang otomatis terhapus
    fd, temp_mdb_path = tempfile.mkstemp(suffix=".mdb")
    os.close(fd)
    
    try:
        # Unduh berkas MDB ke temp file (TIDAK menyimpannya secara permanen)
        try:
            urllib.request.urlretrieve(URL_MDB, temp_mdb_path)
        except Exception as e:
            write_log(f"GAGAL: Peladen Jembatan Windows tidak dapat dihubungi ({str(e)}). Sinkronisasi dibatalkan.")
            return

        # Ekstrak USERINFO
        try:
            userinfo_csv = subprocess.check_output(["mdb-export", temp_mdb_path, "USERINFO"]).decode('utf-8')
        except FileNotFoundError:
            write_log("GAGAL: Paket 'mdbtools' tidak ditemukan di sistem. Harap instal via apt-get.")
            return
            
        # Ekstrak CHECKINOUT
        checkin_csv = subprocess.check_output(["mdb-export", temp_mdb_path, "CHECKINOUT"]).decode('utf-8')
        
        # Parse CSV
        reader_users = csv.DictReader(io.StringIO(userinfo_csv))
        reader_logs = csv.DictReader(io.StringIO(checkin_csv))
        
        # Koneksi Database
        db_config = load_db_config()
        conn = pymysql.connect(**db_config)
        cursor = conn.cursor()
        
        # Upsert USERINFO
        inserted_users = 0
        for row in reader_users:
            try:
                # Kolom penting: USERID, Badgenumber, Name
                userid = row.get("USERID")
                badge = row.get("Badgenumber")
                name = row.get("Name")
                if userid and badge:
                    sql = """
                        INSERT INTO gkr_userinfo (USERID, Badgenumber, Name) 
                        VALUES (%s, %s, %s) 
                        ON DUPLICATE KEY UPDATE Badgenumber=VALUES(Badgenumber), Name=VALUES(Name)
                    """
                    cursor.execute(sql, (userid, badge, name))
                    inserted_users += 1
            except Exception as e:
                pass
                
        # Upsert CHECKINOUT
        inserted_logs = 0
        for row in reader_logs:
            try:
                userid = row.get("USERID")
                checktime = row.get("CHECKTIME")
                checktype = row.get("CHECKTYPE")
                if userid and checktime:
                    # mdb-export format timestamp is 'MM/DD/YY HH:MM:SS'
                    try:
                        dt = datetime.strptime(checktime, '%m/%d/%y %H:%M:%S')
                        mysql_checktime = dt.strftime('%Y-%m-%d %H:%M:%S')
                    except ValueError:
                        mysql_checktime = checktime # Fallback string if format differs
                        
                    sql = """
                        INSERT INTO gkr_checkinout (USERID, CHECKTIME, CHECKTYPE) 
                        VALUES (%s, %s, %s) 
                        ON DUPLICATE KEY UPDATE CHECKTYPE=VALUES(CHECKTYPE)
                    """
                    cursor.execute(sql, (userid, mysql_checktime, checktype))
                    inserted_logs += 1
            except Exception as e:
                pass
                
        conn.commit()
        cursor.close()
        conn.close()
        
        write_log(f"BERHASIL: Tabel disinkronisasi. ({inserted_users} profil, {inserted_logs} rekaman waktu disuntikkan).")
        
    finally:
        # Selalu pastikan file raksasa terhapus, tidak disimpan di disk
        if os.path.exists(temp_mdb_path):
            os.remove(temp_mdb_path)

if __name__ == "__main__":
    fetch_and_sync()
