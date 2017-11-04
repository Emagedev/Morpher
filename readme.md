# API сайта morpher.ru для Magento 1.9

## Склонятор для Magento
Этот модуль позволит склонять слова в зависимости от контекста с помощью онлайн
сервиса morpher.ru. Первый запрос осуществляется через REST API, дальнейшие - из
кэша в базе данных, что позволяет не нагружать сервис лишними запросами (они ограничены
по бесплатной и платной подпискам).

Может быть полезно для склонения [количеств товаров](#inflectWordByNumber) или [имен пользователей](#inflectName), ну и для чего-нибудь ещё.

**Все передаваемые в модуль слова и фразы должны быть в *именительном* падеже**

## Использование с числами
<a href="#inflectWordByNumber" name="inflectWordByNumber">#</a> Mage::helper('morpher')-><b>inflectWordByNumber($number, $phrase [, $keepNumber = false [, $translate = false ]])</b>

Функция поможет склонять слова рядом с числами, например, для количества товаров в каталоге или корзине.

* `$number` определяет число, относительно которого нужно склонить слово
* `$phrase` определяет фразу или слово для склонения
* `$keepNumber` определяет, стоит ли оставлять число <b>перед</b> словом или фразой
* `$translate` определяет, необходимо ли переводить слово или фразу перед склонением, и каким модулем (может быть строкой, например `yourmoule`, или логическим значением)

Пример:

```php
Mage::helper('morpher')->inflectWordByNumber(5, 'item', true, 'catalog');
```

Вернет:
> 5 товаров

```php
Mage::helper('morpher')->inflectWordByNumber(1, 'piece', false, 'yourmodule');
```

Вернет:
> 1 штука

<em>В вашем модуле `thing` должно быть переведено как `штука`</em>

## Склонение имён
<a href="#inflectName" name="inflectName">#</a> Mage::helper('morpher')-><b>inflectName($name, $inflection [, $flags = array()])</b>

Функция поможет склонять имена ваших пользователей.

* `$name` имя пользователя
* `$inflection` [склонение](#declension)
* `$flags` определяет дополнительные [флаги](#flags) для более точного склонения

API неплохо различает имена, но если есть возможность, следует указать род в флагах или использовать следующие методы:

* `inflectMaleName($name, $inflection [, $flags = array()])` для мужских
* `inflectFemaleName($name, $inflection [, $flags = array()])` для женских

Пример:

```php
Mage::helper('morpher')->inflectMaleName('Ломоносов, Михаил Васильевич', Emagedev_Morpher_Helper_Data::DATIVE);
```

Вернет:
> Ломоносову, Михаилу Васильевичу

```php
Mage::helper('morpher')->inflectFemaleName('Любовь Эдуардовна Соболь', Emagedev_Morpher_Helper_Data::GENITIVE);
```

Вернет:
> Любови Эдуардовны Соболь

## Общий случай
<a href="#inflectWord" name="inflectWord">#</a> Mage::helper('morpher')-><b>inflectWord($phrase, $inflection [, $multi = false [, $flags = array() [, $translate = false ]]])</b>

Функция склоняет слово с заданными параметрами.

* `$phrase` фраза или слово для склонения
* `$inflection` [склонение](#declension)
* `$multi` определяет множественное ли число
* `$flags` определяет дополнительные [флаги](#flags) для более точного склонения
* `$translate` определяет, необходимо ли переводить слово или фразу перед склонением, и каким модулем (может быть строкой, например `yourmoule`, или логическим значением)

## Авторизация на morpher.ru

Авторизация необходима для расширения лимита на склонение слов.
Ваш логин и пароль вы можете ввести в панели управления в конфигурации.
Настройки расположены в группе API (в стандартном переводе `СЕРВИСЫ`) во
вкладке `API сайта morpher.ru`.

## Шпаргалка

### <a href="#declension" name="declension">#</a> Склонения

Если ваш кодстайл не позволяет напрямую использовать кириллицу в коде, для склонений есть алиасы, 
записанные как константы в хелпере `Emagedev_Morpher_Helper_Data`.

| Падеж             | Символ | Алиас                                                     |
| ------------------|:------:| ----------------------------------------------------------|
| Именительный      | И      | `Emagedev_Morpher_Helper_Data::NOMINATIVE`                |
| Родительный       | Р      | `Emagedev_Morpher_Helper_Data::GENITIVE`                  |
| Дательный         | Д      | `Emagedev_Morpher_Helper_Data::ACCUSATIVE`                |
| Винительный       | В      | `Emagedev_Morpher_Helper_Data::DATIVE`                    |
| Творительный      | Т      | `Emagedev_Morpher_Helper_Data::INSTRUMENTAL`              |
| Предложный        | П      | `Emagedev_Morpher_Helper_Data::PREPOSITIONAL`             |
| Предложный (о)    | П      | `Emagedev_Morpher_Helper_Data::PREPOSITIONAL_WITH_PREFIX` |
| Местный (см. API) | М      | `Emagedev_Morpher_Helper_Data::LOCATION`                  |

### <a href="#flags" name="flags">#</a> Флаги

Нужны для повышения качества склонения.
Есть слова, которые могут склоняться по-разному, например:

* Фамилия Резник склоняется у мужчин и не склоняется у женщин;
* Ростов в творительном падеже будет Ростовым, если это фамилия, и Ростовом, если это город;
* тестер в винительном падеже будет тестера, если это человек, и тестер, если имеется в виду прибор.

*Методы `inflectName`, `inflectMaleName`, `inflectFemaleName` используют необходимые флаги по умолчанию*

| Флаг      | Описание | Алиас                                          |
| ----------|:--------:| -----------------------------------------------|
| Feminine  | И        | `Emagedev_Morpher_Helper_Data::FLAG_FEMININE`  |
| Masculine | Р        | `Emagedev_Morpher_Helper_Data::FLAG_MASCULINE` |
| Animate   | Д        | `Emagedev_Morpher_Helper_Data::FLAG_ANIMATE`   |
| Inanimate | В        | `Emagedev_Morpher_Helper_Data::FLAG_INANIMATE` |
| Common    | Т        | `Emagedev_Morpher_Helper_Data::FLAG_COMMON`    |
| Name      | П        | `Emagedev_Morpher_Helper_Data::FLAG_NAME`      |

### Юнит тесты

Основная логика покрыта, для запуска нужен модуль EcomDev_PHPUnit

---

> **N.B. Не забывайте модифицировать ключи кэша для корректной работы с числами.**

> **N.B. Так как функции модуля - косметические, модуль _как правило_ не поднимает ошибок. Если что-то работает некорректно, стоит посмотреть в логи.**

> **Описание API тут: http://morpher.ru/ws3/**

*Все совпадения с реальными лицами в примере случайны.*