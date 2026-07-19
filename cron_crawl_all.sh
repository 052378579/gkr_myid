#!/bin/bash

# Konfigurasi Telegram
BOT_TOKEN="8784963582:AAG90wLXKxfKEXa3aLy0sxURZbbyrZnqP9Q"
CHAT_ID="8784856529"

# Eksekusi Crawler
cd /var/www/gkr_myid
OUTPUT=$(php spark crawl:run /var/www/FOTO)

# Ambil kesimpulan dari log (baris yang mengandung SELESAI) dan bersihkan warna ANSI
KESIMPULAN=$(echo "$OUTPUT" | grep "SELESAI:" | sed -r "s/\x1B\[([0-9]{1,3}(;[0-9]{1,2})?)?[mGK]//g" 2>/dev/null)

if [ -z "$KESIMPULAN" ]; then
    KESIMPULAN="Proses selesai. (Cek log server untuk detailnya)"
fi

# Susun pesan yang akan dikirim (format HTML)
PESAN="✅ <b>Cronjob Crawler Selesai!</b>%0A%0A<b>Target:</b> Keseluruhan /var/www/FOTO (BUYER, GRACIA, SWATCHES, WEB)%0A<b>Waktu:</b> $(date +'%Y-%m-%d %H:%M:%S' -d '+7 hours') WIB%0A%0A<b>Hasil:</b>%0A$KESIMPULAN"

# Kirim pesan ke API Telegram
curl -s -X POST "https://api.telegram.org/bot${BOT_TOKEN}/sendMessage" \
    -d chat_id="${CHAT_ID}" \
    -d parse_mode="HTML" \
    -d text="${PESAN}" > /dev/null
