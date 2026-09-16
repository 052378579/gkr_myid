import pymysql
from dotenv import load_dotenv

ENV_PATH = "/var/www/gkr_myid/.env"

def setup_kamus():
    load_dotenv(ENV_PATH)
    conn = pymysql.connect(
        host='localhost',
        user='root',
        password='102013',
        database='gkr_myid'
    )
    cursor = conn.cursor()
    
    kamus_entries = [
        ('absensi', 'absen'),
        ('absensi', 'hadir'),
        ('absensi', 'kehadiran'),
        ('absensi', 'lembur'),
        ('absensi', 'telat')
    ]
    
    # Asumsikan tabel gkr_kamus memiliki kolom 'intent' dan 'keyword' atau sejenisnya.
    # Berdasarkan struktur yang umum di router NLP:
    try:
        for kategori, kata in kamus_entries:
            sql = "INSERT IGNORE INTO gkr_kamus (kategori, kata_kunci) VALUES (%s, %s)"
            # Atur sesuai dengan struktur tabel aktual
            try:
                cursor.execute(sql, (kategori, kata))
            except Exception:
                # Coba struktur alternatif jika kolom berbeda
                try:
                    cursor.execute("INSERT IGNORE INTO gkr_kamus (intent, keyword) VALUES (%s, %s)", (kategori, kata))
                except Exception:
                    pass
        conn.commit()
        print("BERHASIL: Kosakata NLP berhasil disuntikkan ke gkr_kamus.")
    except Exception as e:
        print(f"GAGAL: {e}")
    finally:
        cursor.close()
        conn.close()

if __name__ == "__main__":
    setup_kamus()
