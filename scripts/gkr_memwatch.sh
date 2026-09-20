#!/bin/bash
# --- KONFIGURASI AMBANG BATAS ---
# Ubah nilai THRESHOLD sesuai kebutuhan (misal: 85%)
THRESHOLD=90

# Ambil data total dan penggunaan RAM fisik dari perintah 'free'
TOTAL_MEM=$(free | grep Mem | awk '{print $2}')
USED_MEM=$(free | grep Mem | awk '{print $3}')

# Hitung persentase penggunaan RAM
if [ "$TOTAL_MEM" -gt 0 ]; then
    PERCENT_MEM=$(( 100 * USED_MEM / TOTAL_MEM ))
else
    PERCENT_MEM=0
fi

# Jika penggunaan RAM melewati atau sama dengan ambang batas
if [ "$PERCENT_MEM" -ge "$THRESHOLD" ]; then
    # Ambil timestamp waktu kejadian
    WAKTU=$(date '+%d-%m-%Y %H:%M:%S WIB')
    
    # Susun pesan darurat sesuai standardisasi visual pelaporan sistem
    PESAN="🚨 PERINGATAN KRITIS: Server (DEV)%0A%0A🖥️ Server: 10.147.17.40 (DEV)%0A📉 Use RAM: ${PERCENT_MEM}%25 (Batas: ${THRESHOLD}%25)%0A⏰ Waktu: ${WAKTU}%0A%0A👉 Segera periksa beban Memory (RAM)"

    # Panggil helper/fungsi pengiriman Telegram via script PHP CodeIgniter yang memanfaatkan telegram_helper.php
    php /var/www/gkr_myid/spark telegram:send-alert "$PESAN"
fi
