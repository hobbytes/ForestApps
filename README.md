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
Для обработки событий используется JQuery-функция **.load()**

```HTML
<script>
function EventFunction<?echo $AppID?>(){
  $("#<?echo $AppID?>").load("<?echo $folder?>/main.php?id=<?echo rand(0,10000).'&destination='.$Folder.'&appname='.$AppName.'&appid='.$AppID?>")
  };
</script>
```

Для упрощения разработки приложения используется библиотека **[Mercury](https://github.com/hobbytes/ForestOS/tree/master/system/core/library/Mercury)**, которая поставляется вместе с ОС (начиная с версии 1.0.8.4).
