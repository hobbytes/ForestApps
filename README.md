# Как создавать приложения для Forest OS

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
<?php

  $AppName = $_GET['appname'];
  $AppID = $_GET['appid'];
  $isMobile = $_GET['mobile'];
  $Folder = $_GET['destination'];
  $getValue = $_GET['key']; // любой ключ который вы отправляли

?>
```

Пишем первое приложение
--------------------------------------------
Для упрощения разработки приложения используется библиотека **[Mercury](https://github.com/hobbytes/ForestOS/tree/master/system/core/library/Mercury.AppContainer.php)**, которая поставляется вместе с ОС (начиная с версии 1.0.8.4).

<img src="http://forest.hobbytes.com/media/os/Documentation/Mercury_Logo.png" width="256">

На данный момент существуют три метода:

StartContainer()
--------------------------------------------
Этот метод упрощает создание контейнера. Аргументы, где в описании есть строка "*по умолчанию*" - не обязательны. 

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

Аргументы необходимо объявлять заранее, например так:

```PHP
<?php

  $AppContainer = new AppContainer; // создаем новый объект
  $AppContainer->AppNameInfo = 'App Name';  // объявляем аргумент
  
?>
```

EndContainer()
--------------------------------------------
Аргументы отсутствуют, метод закрывает предыдущий метод, а также вызывает JS-функцию для перерисовки окна:
```JS
UpdateWindow(AppID,AppName);
```

Event(FunctionName, Argument = NULL, Folder, File, RequestData = array())
--------------------------------------------
Данный метод позволяет создать новое событие (JS-функцию). Все вспомогательные данные (AppName, AppID) передаются автоматически. 

| Тип | Аргумент | Описание |
| ------ | ------ | ------ |
| *string* | FunctionName | Имя функции |
| *string* | Argument | Аргумент функции |
| *string* | Folder | Путь до приложения |
| *string* | File  | Имя исполняемого файла без расширения |
| *array* | RequestData | Запрашиваемые данные ("key1" => "value1", "keyN" => "valueN")  |

Аргументы для данного метода задаются сразу, например:

```PHP
<?php

  $AppContainer->Event("TestFunction", 'element', 'system/apps/App_Name/', 'main', array('key' => '"+element.id+"'));

?>
```
Этот метод вернет следующее:
```JS
function TestFunction1(element){
      $("#1").load("system/apps/App_Name/main.php?id=2704&destination=system/apps/App_Name/&appname=App_Name&appid=1&key="+element.id)
    };
```

Рассмотрим простой пример
--------------------------------------------

```PHP
<?php

  /*--------Get App Name and App ID--------*/
  $AppName = $_GET['appname'];
  $AppID = $_GET['appid'];
  
  /*--------Require Mercury library--------*/
  require $_SERVER['DOCUMENT_ROOT'].'/system/core/library/Mercury/AppContainer.php';
  
  /* Make new container */
  $AppContainer = new AppContainer;
  
  /* App Info */
  $AppContainer->AppNameInfo = 'App Name'; // app name information @string
  $AppContainer->SecondNameInfo = 'Second Name'; // second app name information @string
  $AppContainer->VersionInfo = '1.0';  // app version @string
  $AppContainer->AuthorInfo = 'Author'; // app version @string
  
  /* Library List */
  $AppContainer->LibraryArray = Array(); // get libraries @array *not necessary
  
  /* Container Info */
  $AppContainer->appName = $AppName; // app container name @string
  $AppContainer->appID = $AppID; //  app container ID @integer
  $AppContainer->backgroundColor = '#f2f2f2'; // custom background-color *not necessary
  $AppContainer->fontColor = '#000'; // custom font color *not necessary
  $AppContainer->height = '400px';  // app container height @string *not necessary
  $AppContainer->width = '400px'; // app container width @string *not necessary
  $AppContainer->customStyle = ''; // custom CSS style @string *not necessary
  $AppContainer->showError = false; // error display @boolean *not necessary
  
  /* start app container */
  $AppContainer->StartContainer();
  
  /*
  $isMobile - click or touch event,
  $folder - application directory
  */
  
  $isMobile = $_GET['mobile'];
  $folder = $_GET['destination'];
  
  /* print Hello World! */
  echo 'Hello World!';
  
  /* draw button */
  echo '<div class="ui-forest-button ui-forest-accept ui-forest-center">Button</div>';
  
  /* end app container */
  $AppContainer->EndContainer();
  
?>

<script>
/*--------JS Logic--------*/
</script>
```

Результат:

<img src="http://forest.hobbytes.com/media/os/Documentation/test_app.png" width="512">
