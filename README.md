# Модуль оплаты Cs-cart Рассрочка 0-0-4

## Установка

* Создайте резервную копию вашего магазина и базы данных
* Загрузите [cs-cart-factoring004.zip](https://github.com/bnpl-partners/factoring004-cscart.git?raw=true)
* Зайдите в панель администратора Cs-Cart (www.yoursite.com/admin)
* Пройдите _Модули → Управление модулями_
* Выберите **Ручная установка** и загруите архив
* Найдите в списке плагинов _Рассрочка 0-0-4_ и нажмите установить.

![Activate](https://github.com/bnpl-partners/factoring004-cscart/raw/main/doc/install.png)
![Activate](https://github.com/bnpl-partners/factoring004-cscart/raw/main/doc/install2.png)

## Настройка

* Пройдите в _Администрирование → Способы оплаты -> Нажмите добавить
* Выберите процессор `Рассрочка 0-0-4`
* Получите данные от АПИ платежной системы
* Заполните данные
* нажмите _Создать_

![Setup-2](https://github.com/bnpl-partners/factoring004-cscart/raw/main/doc/activate.png)

Модуль настроен и готов к работе.

## Примечания

Разработанно и протестированно с:

* Cs-Cart 4.14.x
* PHP >= 7.4

## Тестирование

Вы можете использовать тестовые данные выданные АПИ платежной системы, чтобы настроить способ оплаты в тестовом режиме