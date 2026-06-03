# دليل تثبيت ونشر مشروع Ascend Academy على السيرفر (Deployment Guide)

هذا الدليل يشرح بالتفصيل كيفية رفع المشروع على GitHub وتثبيته وتشغيله على السيرفر في بيئة الإنتاج (Production)، لضمان أفضل أداء وأعلى درجات الأمان.

---

## 📌 الفهرس
1. [الرفع والتحديث باستخدام GitHub](#1-الرفع-والتحديث-باستخدام-github)
2. [الخيار الأول: التثبيت على سيرفر خاص VPS (Ubuntu + Nginx + PHP-FPM) - الموصى به](#2-الخيار-الأول-التثبيت-على-سيرفر-خاص-vps-ubuntu--nginx--php-fpm---الموصى-به)
3. [الخيار الثاني: التثبيت على استضافة مشتركة (cPanel)](#3-الخيار-الثاني-التثبيت-على-استضافة-مشتركة-cpanel)
4. [أوامر تسريع الأداء وتحسين الأمان في بيئة الإنتاج](#4-أوامر-تسريع-الأداء-وتحسين-الأمان-في-بيئة-الإنتاج)

---

## 1. الرفع والتحديث باستخدام GitHub

عند العمل على المشروع محلياً وتريد رفع التعديلات إلى مستودع GitHub الخاص بك، اتبع الخطوات التالية في Terminal الخاص بك:

```bash
# 1. إضافة كل الملفات المعدلة والجديدة
git add .

# 2. تسجيل التعديلات مع رسالة توضيحية
git commit -m "feat: audit & optimize system, add news permissions, and setup deployment guide"

# 3. رفع التعديلات إلى الفرع الرئيسي (main)
git push origin main
```

---

## 2. الخيار الأول: التثبيت على سيرفر خاص VPS (Ubuntu + Nginx + PHP-FPM) - الموصى به

هذا هو الخيار الأفضل لتطبيقات Laravel لأنه يوفر بيئة سريعة، آمنة، ويدعم تشغيل المهام بالخلفية (Queues & WebSockets) بكفاءة عالية.

### الخطوة 1: تثبيت الحزم الأساسية على السيرفر
اتصل بالسيرفر عبر SSH ونفذ الأوامر التالية لتحديث السيرفر وتثبيت المتطلبات:
```bash
sudo apt update && sudo apt upgrade -y

# تثبيت PHP والإضافات المطلوبة لـ Laravel
sudo apt install php8.2 php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-gd php8.2-intl -y

# تثبيت خادم الويب Nginx وقاعدة البيانات MySQL
sudo apt install nginx mysql-server git unzip curl -y
```

### الخطوة 2: تثبيت Composer و Node.js
```bash
# تثبيت Composer عالمياً
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# تثبيت Node.js و NPM (لـ Vite)
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs
```

### الخطوة 3: سحب المشروع من GitHub
قم بإنشاء مجلد للمشروع وسحب الكود:
```bash
cd /var/www
# استبدل بالرابط الخاص بمستودعك
sudo git clone https://github.com/mostafaelkholy50/Ascend-Academy.git ascend-academy
sudo chown -R $USER:$USER /var/www/ascend-academy
cd ascend-academy
```

### الخطوة 4: تثبيت الاعتماديات (Dependencies)
```bash
# تثبيت مكاتب PHP الخاصة بالإنتاج
composer install --no-dev --optimize-autoloader

# تثبيت مكاتب Javascript وبناء ملفات الواجهة (Vite Assets)
npm install
npm run build
```

### الخطوة 5: إعداد ملف البيئة `.env` وقاعدة البيانات
```bash
# نسخ ملف الإعدادات الافتراضي
cp .env.example .env

# توليد مفتاح تشفير التطبيق
php artisan key:generate
```
قم بفتح ملف `.env` وتعديل البيانات لتناسب السيرفر (بيانات قاعدة البيانات، رابط الموقع، إلخ):
```bash
nano .env
```
أهم المتغيرات التي يجب تعديلها:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ascend_db
DB_USERNAME=ascend_user
DB_PASSWORD=your_secure_password
```

### الخطوة 6: تشغيل الـ Migrations والـ Seeders
```bash
php artisan migrate --force
php artisan db:seed --force
```

### الخطوة 7: ضبط الصلاحيات (مهم جداً للأمان والتشغيل)
يحتاج خادم الويب (Nginx) إلى صلاحيات كتابة في مجلدي `storage` و `bootstrap/cache`:
```bash
sudo chown -R www-data:www-data /var/www/ascend-academy/storage /var/www/ascend-academy/bootstrap/cache
sudo chmod -R 775 /var/www/ascend-academy/storage /var/www/ascend-academy/bootstrap/cache

# إنشاء رابط مجلد التخزين العام
php artisan storage:link
```

### الخطوة 8: إعداد خادم الويب Nginx
قم بإنشاء ملف إعدادات جديد للموقع:
```bash
sudo nano /etc/nginx/sites-available/ascend-academy
```
أضف الإعدادات التالية (مع استبدال `your-domain.com` بنطاقك الفعلي):
```nginx
server {
    listen 80;
    server_name your-domain.com www.your-domain.com;
    root /var/www/ascend-academy/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```
ثم قم بتفعيل الإعدادات وإعادة تشغيل Nginx:
```bash
sudo ln -s /etc/nginx/sites-available/ascend-academy /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

### الخطوة 9: تفعيل شهادة الأمان SSL مجاناً (Let's Encrypt)
```bash
sudo apt install certbot python3-certbot-nginx -y
sudo certbot --nginx -d your-domain.com -d www.your-domain.com
```

### الخطوة 10: تشغيل الـ Scheduler والـ Queue (Supervisor)
إذا كان المشروع يحتوي على مهام مجدولة أو إرسال إيميلات بالخلفية:
1. **الـ Scheduler**: قم بإضافة سطر الـ Cron التالي للسيرفر:
   ```bash
   * * * * * cd /var/www/ascend-academy && php artisan schedule:run >> /dev/null 2>&1
   ```
2. **الـ Queue Worker (Supervisor)**:
   قم بتثبيت Supervisor لتشغيل خادم الطابور بالخلفية بشكل مستمر:
   ```bash
   sudo apt install supervisor -y
   ```
   أنشئ ملف إعدادات جديد:
   ```bash
   sudo nano /etc/supervisor/conf.d/ascend-worker.conf
   ```
   أضف التالي:
   ```ini
   [program:ascend-worker]
   process_name=%(program_name)s_%(process_num)02d
   command=php /var/www/ascend-academy/artisan queue:work --sleep=3 --tries=3 --max-time=3600
   autostart=true
   autorestart=true
   stopasgroup=true
   killasgroup=true
   user=www-data
   numprocs=2
   redirect_stderr=true
   stdout_logfile=/var/www/ascend-academy/storage/logs/worker.log
   stopwaitsecs=3600
   ```
   ثم شغله:
   ```bash
   sudo supervisorctl reread
   sudo supervisorctl update
   sudo supervisorctl start ascend-worker:*
   ```

---

## 3. الخيار الثاني: التثبيت على استضافة مشتركة (cPanel)

إذا كنت تستخدم استضافة مشتركة تدعم لوحة التحكم cPanel، اتبع الخطوات التالية:

### الخطوة 1: إعداد الملفات وضغطها
1. قم بضغط مجلد المشروع بالكامل في جهازك المحلي بصيغة `.zip` (تأكد من عدم تضمين مجلد `node_modules` أو `vendor` لتقليل الحجم، حيث سنقوم بتثبيتهم أو رفعهم بشكل منفصل، أو قم بضغطهم إذا لم يكن لديك صلاحيات Terminal في الاستضافة).
2. ارفع الملف المضغوط إلى لوحة التحكم cPanel باستخدام **File Manager** وضعه في المجلد الرئيسي (خارج مجلد `public_html`).
3. قم بفك الضغط عن الملف هناك.

### الخطوة 2: التعامل مع المجلد العام `public`
في Laravel، يجب أن يشير نطاق موقعك إلى مجلد `public`. في الاستضافات المشتركة، يشير النطاق افتراضياً إلى مجلد `public_html`. لحل هذه المشكلة بأمان:
- قم بنقل كافة محتويات مجلد `public` الخاص بالمشروع إلى مجلد `public_html` في الاستضافة.
- افتح ملف `index.php` الموجود الآن داخل `public_html` وعدّل مسارات استدعاء الملفات لتبدو كالتالي (على افتراض أن مجلد المشروع بجانب `public_html`):
  ```php
  // سطر 47 تقريباً
  require __DIR__.'/../ascend-academy/vendor/autoload.php';

  // سطر 61 تقريباً
  $app = require_once __DIR__.'/../ascend-academy/bootstrap/app.php';
  ```

### الخطوة 3: إعداد قاعدة البيانات والـ `.env`
1. من لوحة cPanel، ادخل إلى **MySQL Database Wizard** وأنشئ قاعدة بيانات جديدة ومستخدم جديد بجميع الصلاحيات.
2. اذهب إلى مجلد المشروع الرئيسي عبر File Manager، وقم بتعديل ملف `.env` (إذا كان مخفياً، تأكد من تفعيل "Show Hidden Files" من إعدادات File Manager).
3. حدّث متغيرات قاعدة البيانات ورابط الموقع (`APP_URL`) وقم بتغيير `APP_DEBUG` إلى `false`.

### الخطوة 4: تشغيل الـ Migrations وتثبيت الاعتماديات
- إذا كانت استضافتك توفر **Terminal**، يمكنك الدخول وتشغيل:
  ```bash
  composer install --no-dev --optimize-autoloader
  php artisan migrate --force
  php artisan db:seed --force
  php artisan storage:link
  ```
- إذا لم تكن هناك صلاحية Terminal:
  - يجب رفع مجلد `vendor` بعد عمل `composer install --no-dev` محلياً.
  - لتشغيل الـ migrations، يمكنك إضافة مسار مؤقت في ملف `routes/web.php` لتشغيل الأمر برمجياً ثم حذفه فوراً:
    ```php
    Route::get('/run-migrations', function () {
        Artisan::call('migrate:fresh', ['--force' => true]);
        Artisan::call('db:seed', ['--force' => true]);
        return "Database migrated and seeded successfully!";
    });
    ```
    قم بزيارة الرابط `yourdomain.com/run-migrations` مرة واحدة، ثم **قم بحذف المسار فوراً من ملف الراوتس لأسباب أمنية**.

### الخطوة 5: إعداد المهام المجدولة وطابور الرسائل (Cron Jobs & Queue Worker)
بما أن بعض الإيميلات مثل إشعارات الحصص والمدفوعات يتم إرسالها بالخلفية عبر الطوابير (Queues)، يجب عليك إعداد مهمتين مجدولتين (Cron Jobs) في لوحة التحكم (cPanel أو Hostinger):

#### المهمة الأولى: تشغيل المجدول (Laravel Scheduler)
هذه المهمة مسؤولة عن التحقق من وجود حصص أو مدفوعات تحتاج لتنبيهات بشكل دوري.
- **التوقيت:** كل دقيقة (`* * * * *`).
- **الأمر:**
  ```bash
  /usr/local/bin/php /home/username/ascend-academy/artisan schedule:run >> /dev/null 2>&1
  ```
  *(أو يمكنك استخدام ملف `cron-scheduler.php` المرفق في جذر المشروع إذا كانت الاستضافة لا تدعم تشغيل artisan مباشرة):*
  ```bash
  /usr/local/bin/php /home/username/ascend-academy/cron-scheduler.php >> /dev/null 2>&1
  ```

#### المهمة الثانية: تشغيل طابور الرسائل (Queue Worker)
هذه المهمة مسؤولة عن إرسال الإيميلات الفعلية التي تم جدولتها في قاعدة البيانات. بدونها ستظل الرسائل معلقة في جدول `jobs`.
- **التوقيت:** كل دقيقة (`* * * * *`).
- **الأمر:**
  ```bash
  /usr/local/bin/php /home/username/ascend-academy/artisan queue:work --stop-when-empty >> /dev/null 2>&1
  ```
  *(أو يمكنك استخدام ملف `queue.php` المرفق في جذر المشروع والذي تم تحسينه لمعالجة كافة المهام دفعة واحدة ثم التوقف):*
  ```bash
  /usr/local/bin/php /home/username/ascend-academy/queue.php >> /dev/null 2>&1
  ```

> 💡 **خيار بديل وسهل:** إذا كنت لا ترغب في إعداد مهمة Cron ثانية للـ Queue Worker، يمكنك تعديل ملف `.env` على السيرفر وجعل قيمة `QUEUE_CONNECTION=sync`. هذا سيجعل النظام يرسل الإيميلات مباشرة وتلقائياً بمجرد تشغيل المجدول، ولكن قد يأخذ المجدول وقتاً أطول في التنفيذ.

*(ملاحظة: تأكد من كتابة المسار الصحيح لإصدار الـ PHP ومجلد مشروعك في الاستضافة بدلاً من /home/username/)*.

---

## 4. أوامر تسريع الأداء وتحسين الأمان في بيئة الإنتاج

لضمان عمل تطبيق Laravel بأقصى سرعة ممكنة وأعلى أمان في الإنتاج، نفذ الأوامر التالية بعد كل عملية تحديث للكود (Deployment):

```bash
# 1. كاش لإعدادات التطبيق لعدم قراءتها من الملفات كل مرة
php artisan config:cache

# 2. كاش لملفات المسارات (Routing) لتسريع التوجيه
php artisan route:cache

# 3. كاش لملفات العرض (Blade Views)
php artisan view:cache

# 4. مسح الكاش القديم عند التحديث
php artisan cache:clear
```
> ⚠️ **ملاحظة هامة:** لا تستخدم أمري `config:cache` أو `route:cache` في جهازك المحلي (Local Development)، بل استخدمهم فقط على سيرفر الإنتاج.
