#!/bin/bash

# Konfigurasi Telegram
BOT_TOKEN="8784963582:AAG90wLXKxfKEXa3aLy0sxURZbbyrZnqP9Q"
CHAT_ID="8784856529"

# Eksekusi AI Trainer Engine (MobileNetV3 + FAISS)
cd /var/www/gkr_myid
OUTPUT=$(/mnt/sdcard/ai-scanner/env-ai/bin/python /var/www/gkr_myid/python_services/buat_index.py 2>&1)

# Bersihkan warna ANSI dari output Python
CLEAN_OUTPUT=$(echo "$OUTPUT" | sed -r "s/\x1B\[([0-9]{1,3}(;[0-9]{1,2})?)?[mGK]//g" 2>/dev/null)

# Ambil kesimpulan (baris yang mengandung BERHASIL atau ERROR)
KESIMPULAN=$(echo "$CLEAN_OUTPUT" | grep -E "BERHASIL:|ERROR:" | tail -n 1)

if [ -z "$KESIMPULAN" ]; then
    KESIMPULAN="Proses AI Trainer selesai (Cek log untuk detail anomali)."
fi

# Susun pesan yang akan dikirim (format HTML)
PESAN="<b>AI Trainer Selesai!</b>%0A%0A<b>Target:</b> /var/www/FOTO%0A<b>Waktu:</b> $(date +'%d-%m-%Y %H:%M:%S' -d '+7 hours') WIB%0A%0A<b>Hasil Eksekusi:</b>%0A$KESIMPULAN"

# Kirim pesan ke API Telegram
curl -s -X POST "https://api.telegram.org/bot${BOT_TOKEN}/sendMessage" \
    -d chat_id="${CHAT_ID}" \
    -d parse_mode="HTML" \
    -d text="${PESAN}" > /dev/null
