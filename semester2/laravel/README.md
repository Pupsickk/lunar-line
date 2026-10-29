# ЛР2.1 — Laravel MVC и сохранение заказов

Это отдельное серверное приложение Laravel 11 для темы «Лунная линия». Предыдущая статическая версия первого семестра находится в ../../dist и не заменяется. Запуск этого приложения требует PHP 8.2+, Composer и MySQL/MariaDB, для тестов также pdo_sqlite.

## Запуск в Windows с XAMPP

1. Включите Apache и MySQL в XAMPP. Добавьте C:\xampp\php в PATH, установите Composer. Проверьте `php -v` и `composer -V`.
2. В phpMyAdmin создайте БД `lunar_line` с utf8mb4_unicode_ci. Либо выполните `CREATE DATABASE lunar_line CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;`.
3. Откройте терминал в `semester2/laravel` и выполните:

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
```

4. Укажите свои DB_USERNAME и DB_PASSWORD в `.env`. Пустой пароль root подходит только для стандартной локальной установки XAMPP и не должен переноситься на публичный сервер.
5. Выполните:

```powershell
php artisan migrate --seed
php artisan app:create-admin seller@example.test
php artisan serve --host=127.0.0.1 --port=8000
```

Команда создания администратора попросит пароль скрытым вводом (от 12 символов). Пароль не задан в исходниках. Откройте http://127.0.0.1:8000. Оплата и рассылка писем не подключены.

## Как проверить настоящий заказ

1. Откройте каталог, добавьте «Один вопрос», поставьте количество 2.
2. Оформите на вымышленное имя и `test@example.test`.
3. Получите страницу «Заказ сохранён» с UUID и суммой 1580.00 ₽. Данные уже находятся в MySQL, а не в localStorage.
4. Обновите закрытую ссылку — заказ останется доступен. Без токена сервер отдаёт 404. Ссылку нельзя публиковать.
5. Откройте `/admin/orders`, войдите созданным администратором — заказ виден продавцу. Обычный пользователь получает 403, гость перенаправляется на вход.
6. В phpMyAdmin проверьте `orders` и `order_product`. Цена фиксируется в позиции заказа. Цена с клиента игнорируется. Количество доступных сессий уменьшается внутри транзакции.

## Архитектура

Маршруты routes/web.php → контроллеры → модели Eloquent → Blade. Все формы POST/PATCH содержат CSRF. Каталог фильтруется по теме и границам цены, сортируется и использует пагинацию. Главная загружает категории, хиты и новые услуги из БД. Страница расклада загружает категорию и одобренные отзывы с автором через eager loading.

Связи: User–Profile hasOne, User–Order hasMany, Category–Product hasMany/belongsTo, Category–Category parent/children, Product–Order belongsToMany через order_product, Product–Review и User–Review. OrderItem отдельно отображает ту же промежуточную таблицу.

`DatabaseSeeder` создаёт 3 категории, 6 реальных для учебного каталога услуг и учебные отзывы. Заказы появляются только при оформлении. Фабрики предназначены для тестов и расширения набора данных.

## Проверки

```text
php artisan route:list
php artisan view:cache
vendor/bin/phpunit
php artisan migrate:rollback
php artisan migrate --seed
```

Откат удаляет таблицы и заказы, выполняйте его только на учебной базе. Автоматический workflow `.github/workflows/semester2.yml` проверяет страницы, корзину, серверный пересчёт цены, сохранение заказа, доступ к данным и миграции MySQL. Текущий результат смотрите в GitHub Actions, он не считается успешным до завершения запуска.

## Контрольные вопросы

1. Миграции хранят схему в Git и позволяют одинаково создавать/откатывать таблицы на разных машинах.
2. Mass assignment заполняет модель массивом. $fillable ограничивает поля. Роль пользователя не входит в $fillable.
3. Category::products() — hasMany, Product::category() — belongsTo. В products хранится category_id.
4. belongsToMany использует pivot order_product, где лежат quantity, price и снимок product_name.
5. N+1 возникает при дополнительном запросе связи для каждой записи. with('category') заранее загружает категории для всего списка.
6. scopeActive и scopeHits повторно используют условия: Product::active()->hits()->get().
7. hasOne описывает сторону владельца, belongsTo — сторону с внешним ключом.
8. firstOrFail возвращает первую модель или выбрасывает ModelNotFoundException, которую Laravel превращает в 404.
9. return view('catalog', ['products'=>$products]) передаёт переменную в Blade.
10. paginate ограничивает записи страницы и считает страницы. withQueryString сохраняет фильтры в ссылках следующей страницы.
