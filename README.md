# Как создавать приложения для Forest OS

Структура приложения
--------------------------------------------

Каждое приложение в Forest OS это папка, которая расположена в директории **"system/apps/App_Name"** (пробелы в имени приложения строго запрещены, заменяйте их занаком подчеркивания "_"). 

В папке обязательно должны находиться два файла:
1. `<main.php>` - исполняемый файл приложения
2. `<app.png>` - инконка приложения (рекомендуемый размер 256x256)

<img src="http://forest.hobbytes.com/media/os/Documentation/app_dir.png" width="512">

Основыне понятия работы приложений
--------------------------------------------
Приложение в Forest OS представляет собой файл формата ***.php** в котором комбинирют языки PHP, JavaScript(+JQuery) и HTML-разметка.
Загрузка приложения осуществляется с помощью отправки GET-запроса к файлу **[makeprocess.php](https://github.com/hobbytes/ForestOS/tree/master/makeprocess.php)** JS-функцией:
```JS
makeprocess(destination,  key,  value,  name);
```
где **destination** - путь до папки с приложением, **key** - ключ, **value** - значение ключа, **name** - имя программы.
После чего создается контейнер приложения с уникальным ID. 

Вот как выглядит упрощенная схема контейнера:
```HTML
<div id="appID">
  <div id="dragID">
  </div>
  <div id="1" location="system/apps/App_Name/main.php">
    <div id="App_NameID">
     <!--сюда загружается приложение-->
    </div>
  </div>
</div>
```
При загрузки приложения передаются следующие данные: **appname** - название приложения, **appid** - уникальный ID, **destination** - путь до приложения, **mobile** - передает индетификатор устройства (true/false). А так же передается пара **ключ=значение**, если их указали в функции *makeprocess()*. 

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
Для упрощения разработки приложений используется библиотека **[Mercury](https://github.com/hobbytes/ForestOS/tree/master/system/core/library/Mercury)**, которая поставляется вместе с ОС (начиная с версии 1.0.8.4).

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
| *string* | backgroundColor | Цвет фона приложения (по умолчанию *#F2F2F2*) |
| *string* | fontColor | Цвет шрифта (по умолчанию *#000*) |
| *string* | height | Высота (по умолчанию *550px*) |
| *string* | width | Ширина (по умолчанию *800px*) |
| *string* | customStyle | CSS (по умолчанию NULL) |
| *string* | isMobile | Необязательный аргумент, хранит значение "true/false" в зависимости от устройства (по умолчанию NULL) |
| *boolean* | securityMode  | Этот режим не позволяет запускать приложение, если пользователь не в системе (по умолчанию true) |
| *boolean* | showError  | Отображение ошибок (по умолчанию false) |
| *boolean* | showStatistics  | Выводит статистику для контейнера: время и размер (по умолчанию false) |

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

События в приложении
--------------------------------------------
С помощью этих функций можно отслеживать состояние вашего приложения. 

| Название функции | Описание |
| ------ |------ |
| *closeApp* | событие происходит при нажатии кнопки "закрыть приложение" |
| *hideApp* | событие происходит при нажатии кнопки "свернуть приложение" |
| *reloadApp* | событие происходит при нажатии кнопки "перезагрузить приложение" |
| *activeApp* | событие происходит если приложение в активном состоянии |
| *resizeApp* | событие происходит при изменении размера окна приложения |
| *moveApp* | событие происходит если окно приложения перемещается |
| *windowFullScreenApp* | событие происходит если окно приложения развернуто на весь экран |
| *windowNormalScreenApp* | событие происходит если окно приложения переходит из состояния Full Screen => Normal Screen |

Эти функции должны вызываться с идентификатором приложения **AppID**, например:

```JS
function closeApp<?echo $AppID?>(){
  alert('this app is close!');
}
```

Event(FunctionName, Argument = NULL, Folder, File, RequestData = array(), CustomFunction = NULL, CustomContainer = NULL)
--------------------------------------------
Данный метод позволяет создать новое событие (JS-функцию). Все вспомогательные данные (AppName, AppID) передаются автоматически. 

| Тип | Аргумент | Описание |
| ------ | ------ | ------ |
| *string* | FunctionName | Имя функции |
| *string* | Argument | Аргумент функции (по умолчанию NULL) |
| *string* | Folder | Путь до приложения |
| *string* | File  | Имя исполняемого файла без расширения |
| *array* | RequestData | Запрашиваемые данные ("key1" => "value1", "keyN" => "valueN")  |
| *string* | CustomFunction  | Произвольная JS-функция (по умолчанию NULL) |
| *integer* | CustomFunctionMode  | Вставить функцию в начале (0) или в конце (1) (по умолчанию 1) |
| *string* | CustomContainer   | Произвольное имя контейнера (по умолчанию NULL) |

Аргументы для данного метода задаются сразу, например:

```PHP
<?php

  $AppContainer->Event(
   "TestFunction", 
   'element', 
   'system/apps/App_Name/', 
   'main', 
   array('key' => '"+element.id+"'),
   '"+alert(\'Hello World!\')+"',
   1
  );

?>
```
Этот метод вернет следующее:
```JS
/* function TestFunction1 */
function TestFunction1(element){
      $("#1").load("system/apps/App_Name/main.php?id=2704&destination=system/apps/App_Name/&appname=App_Name&appid=1&key="+element.id)
      alert('Hello World!');
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
  $AppContainer->height = '400px';  // app container height @string *not necessary
  $AppContainer->width = '400px'; // app container width @string *not necessary
  
  /* start app container */
  $AppContainer->StartContainer();
  
  /*
  $isMobile - true or false event,
  $folder - application directory
  */
  
  $isMobile = $_GET['mobile'];
  $folder = $_GET['destination'];
  
  /* print Hello World! */
  echo 'Hello World!';
  
  /* draw button */
  echo '<div onClick="test<?echo $AppID?>()" class="ui-forest-button ui-forest-accept ui-forest-center">Button</div>';
  
  /* end app container */
  $AppContainer->EndContainer();
  
?>

<script>
/*--------JS Logic--------*/

//пример события при сворачивании окна
function hideApp<?echo $AppID?>(){
  alert('this app is hide!');
}

<?

//пример Event, который при нажатии кнопки передает get-запрос $_GET['key] значение 'value'
$AppContainer->Event(
  "test",
  "Null",
  $Folder,
  'main',
  array(
    'key' => 'value'
  )
);

?>

</script>
```

Результат:

<img src="http://forest.hobbytes.com/media/os/Documentation/test_app.png" width="512">
