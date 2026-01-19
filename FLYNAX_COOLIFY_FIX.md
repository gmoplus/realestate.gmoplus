# Flynax Coolify Deployment Guide (Güncellenmiş v2)

Bu dosya, **Flynax Real Estate** yazılımının **Coolify** üzerinde çalıştırılması için gerekli tüm adımları içerir.

---

## 📋 Ön Gereksinimler

| Bileşen | Durum | Notlar |
|---------|-------|--------|
| MariaDB/MySQL | ✅ Harici | Coolify'da ayrı servis olarak çalışıyor |
| PHP 8.3 | ✅ Dockerfile içinde | Tüm uzantılar dahil |
| Apache | ✅ Dockerfile içinde | mod_rewrite aktif |

---

## 🔧 Coolify Ortam Değişkenleri

Coolify panelinde **Environment Variables** bölümüne şunları ekleyin:

```env
# Database Connection
DB_HOST=mariadb          # Coolify'daki veritabanı servis adı
DB_PORT=3306
DB_NAME=gmoplus_realestate
DB_USER=gmoplus_realestateuser
DB_PASSWORD=gmoplus_realestateuser1234
DB_PREFIX=fl_

# Application
APP_URL=https://realestate.gmoplus.com
DEBUG=false

# Port (Coolify için gerekli)
PORT=80
```

---

## 🚀 Deployment Adımları

### 1. Git'e Push
```bash
git add .
git commit -m "Coolify deployment fix"
git push origin main
```

### 2. Coolify'da Build
- Coolify panelinde **Deploy** butonuna tıklayın
- Build türü: **Dockerfile** (otomatik algılanır)

### 3. İlk Çalıştırma Sonrası Kontroller

#### A) Veritabanı Bağlantısı Test
Container terminal'inde:
```bash
php -r "
try {
    \$pdo = new PDO('mysql:host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_NAME'), getenv('DB_USER'), getenv('DB_PASSWORD'));
    echo '✅ Veritabanı bağlantısı başarılı!';
} catch (Exception \$e) {
    echo '❌ Hata: ' . \$e->getMessage();
}
"
```

#### B) Admin Şifre Sıfırlama (Gerekirse)
```bash
php -r "
\$pdo = new PDO('mysql:host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_NAME'), getenv('DB_USER'), getenv('DB_PASSWORD'));
\$pdo->exec(\"UPDATE fl_admins SET Pass = MD5('123456') WHERE User = 'admin'\");
echo '✅ Admin şifresi: 123456';
"
```

#### C) Smarty Cache Temizleme
```bash
rm -rf /var/www/html/tmp/compile/*
```

---

## 🔍 Yapılan Düzeltmeler

### 1. `includes/config.inc.php`
SSL proxy fix ve session path düzeltmesi eklendi:
```php
// Session dizinini /tmp olarak ayarla
ini_set('session.save_path', '/tmp');

// Traefik proxy arkasında HTTPS algılama
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
    $_SERVER['SERVER_PORT'] = 443;
}
```

### 2. `.htaccess`
cPanel'e özel direktifler kaldırıldı (php_flag, php_value, AddHandler)

### 3. `Dockerfile` (Yeni)
PHP 8.3 + Apache + tüm gerekli uzantılar

### 4. `nixpacks.toml`
Alternatif olarak Nixpacks desteği (Dockerfile yoksa kullanılır)

---

## ⚠️ Sık Karşılaşılan Sorunlar

### "It works!" Sayfası Görünüyor
**Neden:** DocumentRoot yanlış veya index.php çalışmıyor  
**Çözüm:** Dockerfile kullanın, nixpacks.toml yerine

### 502 Bad Gateway
**Neden:** SSL redirect döngüsü  
**Çözüm:** config.inc.php'deki SSL fix uygulandı

### Admin Giriş Yapılamıyor
**Neden:** Session path yazılamıyor  
**Çözüm:** `ini_set('session.save_path', '/tmp');` eklendi

### Favicon 404
**Neden:** favicon.ico dosyası eksik  
**Çözüm:** Boş dosya oluşturuldu veya gerçek favicon ekleyin

---

## 📁 Dosya Yapısı

```
realestate.gmoplus.com/
├── Dockerfile              ← Coolify build için
├── nixpacks.toml           ← Alternatif (yedek)
├── .htaccess               ← Temizlenmiş
├── includes/
│   └── config.inc.php      ← SSL/Session fix dahil
├── tmp/
│   ├── compile/            ← Smarty şablonları (777)
│   ├── cache/              ← Cache (777)
│   └── upload/             ← Yüklemeler (777)
├── files/                  ← Medya dosyaları (777)
└── plugins/                ← Eklentiler (777)
```

---

## ✅ Kontrol Listesi

- [ ] Coolify'da environment variables ayarlandı
- [ ] Git'e push yapıldı
- [ ] Coolify build başarılı
- [ ] Site yükleniyor (no "It works!")
- [ ] Admin paneli çalışıyor (/admin)
- [ ] Veritabanı bağlantısı OK
- [ ] HTTPS düzgün çalışıyor
