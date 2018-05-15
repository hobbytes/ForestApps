# Как создавать приложения Forest OS

Структура приложения
--------------------------------------------

Каждое приложение в Forest OS это папка, которая расположена в директории **"system/apps/App_Name"** (пробелы в имени приложения строго запрещены, заменяйте их занаком подчеркивания "_"). 

В папке обязательно должны находиться два файла:
1. `<main.php>` - исполняемый файл приложения
2. `<app.png>` - инконка приложения (рекомендуемый размер 256px256px)

<img src="http://forest.hobbytes.com/media/os/Documentation/app_dir.png" width="512">

Основыне понятия работы приложений
--------------------------------------------
Приложение в Forest OS представляет собой файл формата ***.php** в котором комбинирют языки PHP, JavaScript(+JQuery) и HTML-разметка.
Загрузка приложения осуществляется с помощью отправки GET-запроса к файлу **[makeprocess.php](https://github.com/hobbytes/ForestOS/tree/master/makeprocess.php)** JS-функцией:
```JS
makeprocess(destination,  key,  value,  name);
```
где **destination** - путь до папки с приложением, **key** - передача ключа, **value** - передача значения, **name** - имя программы.
После чего создается контейнер приложения с уникальным ID. 

Вот как выглядит упрощенная схема контейнера:
```HTML
<div id="appID">
  <div id="dragID">
  </div>
  <div id="18" location="system/apps/App_Name/main.php">
    <div id="App_NameID">
     <!--сюда загружается приложение-->
    </div>
  </div>
</div>
```
При загрузки приложения передаются следующие данные: **appname** - название приложения, **appid** - уникальный ID, **destination** - путь до приложения, **mobile** - передает индетификатор устройства (если ПК, то передает "dblclick", в противном случае "click"). А так же передается пара **ключ=значение**, если их указали в функции *makeprocess()*. 

Для работы приложения все эти данные необходимо хранить (а также передавать при обработки событий), вот как это делается:

```PHP
$AppName = $_GET['appname'];
$AppID = $_GET['appid'];
$isMobile = $_GET['mobile'];
$Folder = $_GET['destination'];
$getValue = $_GET['key']; // любой ключ который вы отправляли
```

Для обработки событий используется JQuery-функция **.load()**

```HTML
<script>
function EventFunction<?echo $AppID?>(){
  $("#<?echo $AppID?>").load("<?echo $folder?>/main.php?mobile=<?echo $isMobile.'&destination='.$Folder.'&appname='.$AppName.'&appid='.$AppID?>&key1=value1&keyN=valueN")
  };
</script>
```

Пишем первое приложение
--------------------------------------------
Для упрощения разработки приложения используется библиотека **[Mercury](https://github.com/hobbytes/ForestOS/tree/master/system/core/library/Mercury.AppContainer.php)**, которая поставляется вместе с ОС (начиная с версии 1.0.8.4).

На данный момент существуют два метода:

StartContainer()
--------------------------------------------
| Тип | Аргумент | Описание |
| ------ | ------ | ------ |
| *string* | AppNameInfo | Публичное имя приложения |
| *string* | SecondNameInfo | Второе публичное имя приложения (для локализации) |
| *string* | VersionInfo | Версия приложения |
| *string* | AuthorInfo  | Имя автора |
| *array* | LibraryArray | Список подключаемых библиотек |
| *string* | appName | Имя контейнера |
| *string* | appID | Уникальный ID контейнера|
| *string* | backgroundColor | Цвет приложения (по умолчанию *#F2F2F2*) |
| *string* | fontColor | Цвет шрифта (по умолчанию *#000*) |
| *string* | height | высота (по умолчанию *550px*) |
| *string* | width | ширина (по умолчанию *800px*) |
| *string* | customStyle | CSS (по умолчанию NULL) |
| *boolean* | showError  | отображение ошибок (по умолчанию false) |

Этот метод упрощает создание контейнера. Аргументы, где в описании есть строка "*по умолчанию*" - не обязательны

EndContainer()
--------------------------------------------
Аргументы отсутствуют, метод закрывает предыдущий метод, а также вызывает JS-функцию для перерисовки окна:
```JS
UpdateWindow(AppID,AppName);
```
