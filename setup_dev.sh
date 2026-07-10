#!/bin/bash

# 1. Konfigurasi Apache
echo "Konfigurasi Apache untuk foto.budi.biz.id..."
cat <<EOF > /etc/apache2/sites-available/foto.budi.biz.id.conf
<VirtualHost *:80>
    ServerName foto.budi.biz.id
    DocumentRoot /var/www/FOTO

    <Directory /var/www/FOTO>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
EOF

a2ensite foto.budi.biz.id
systemctl reload apache2
echo "Apache berhasil dikonfigurasi dan direload."

# 2. Update Database MySQL
echo "Menjalankan update database..."
mysql -u root -p102013 gkr_myid < /var/www/gkr_myid/update_db.sql
echo "Database berhasil diperbarui."
