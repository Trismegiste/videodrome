# Videodrome
A set of tools for easy creation of video

## What
It's a set of CLI (using Symfony Console component) that create video movie with unconventional multiple sources.
For example it can turn a LibreOffice Impress document and a sound file into a mpeg4 movie.

This app is fully unit & functional tested with PHPUnit.

## How
This is a PHP symfony console command line interface that uses extensively many other installed softwares
like LibreOffice, Plopper, ImageMagick, ffmpeg and Inkscape, depending of what do you want to run.

## Installation
```bash
flo@spin5:~$ git clone https://github.com/Trismegiste/videodrome
flo@spin5:~$ composer.phar install
```

## Tests & code coverage
```bash
flo@spin5:~$ phpdbg -qrr ./vendor/bin/phpunit
```
All code coverage generated html goes into the ./doc folder.

## Documentation

### List of available commands
```bash
flo@spin5:~$ php app.php list
```

### Check for installed softwares
```bash
flo@spin5:~$ php app.php system:check
```

### Conference
This command builds a mpeg4 movie with low framerate from 3 files :
* a LibreOffice Impress document
* a sound file with a recorded voice for example
* an Audacity marker file for timing each slide

```bash
flo@spin5:~$ php app.php conference:build presentation.odp myvoice.mp3 timecode.txt
```

### Advertisement
This command is almost the same as Conference except it generates an animated GIF. 
No sound nor timecode are required. Each frame has a default duration of 5 seconds (can be changed with option '--delay').

```bash
flo@spin5:~$ php app.php conference:gif --delay=10 --width=600 --height=400 presentation.odp
```

### Trailer creation
This set of commands is inteded to rapidly build a trailer-like movie with pictures, movie clips, sound and captions.
It's very convenient for creating an avertising message on Youtube, for example.
You need to provide 7 types of assets :
* a folder of pictures : each picture will be animated with a panning (a sliding effect)
* a config file for panning in the picture folder (default name : 'panning.cfg'). It provides in which direction the panning will be set
* a folder of movie clips : each movie will be cut and resize for the final resolution
* a config file for cutting in the movie folder (default name : 'cutter.cfg'). It provides when the clip has to be cut
* a folder full of SVG for captions. Each SVG will be converted into PNG and overlayed on each clip
* a sound file (any format readable by ffmpeg)
* an Audacity marker file for time each clip

```bash
flo@spin5:~$ php app.php trailer:build ./clip ./slide ./svg epicmusic.ogg timecode.txt
```

Launch the command and wait. You need a lot of temporary disk space as it uses uncompressed video codec to ensure
the best quality before final encoding into mpeg4 format. The video is fully compatible with Youtube (no reencoding needed).

### Trailer dummy build
This command generates dummy assets (svg, png and config files) to test your video. You only need the marker file from Audacity.
It's useful to control if the rythm of the trailer is ok and how many text you can put in your captions.
It checks if files already exist and generates only missing assets.

```bash
flo@spin5:~$ php app.php trailer:dummy ./clip ./slide ./svg timecode.txt
```

### Trailer assets check
Checks if all assets, config files are ok and if there is no missing file need for building the trailer.

```bash
flo@spin5:~$ php app.php trailer:check ./clip ./slide ./svg sound.mp3 timecode.txt
```

### Trailer, other commands
Each step of trailer building could be launch separately :
* Panning creations of pictures
* Cutting clips
* Overlay a set of transparent SVG
* Concatenation of a set of clips
* Muxing a sound into a video

### Editing movie for Youtube
This commmand is intended to create a movie from various sources and encode it for Youtube format.
```bash
flo@spin5:~$ php app.php edit:youtube movie.json
```

It uses a configuration file in json format containing timecode and duration for each clip. 
This file could be manually created or with the help of the commands 'edit:config' and 'edit:sort'

### Editing configuration
This commmand is using 'ffplay' (from ffmpeg) to easily select timecode and duration from a clip. It creates a new entry in a json configuration.
```bash
flo@spin5:~$ php app.php edit:config ./myvideo
```

With the command 'edit:sort', you can re-order the queue in the json configuration file.

## Internals
Each command use the same pattern for atomic conversion of one (or many) media file into one (or many) other media file.
It's loosely based of a Chain of Responsibilities design pattern. You can easily re-use the atomic component to create another movie builder.
The only thing you need to care is the metadata needed for the FileJob subclass (such as width, height, duration...).
Don't panic, if a metadata is missing, it raises a JobException.

This app is very strict and ensure no files are missing or lost in the process. 2 classes (MediaList and MediaFile) implement a
Composite Design Pattern (with only one level of children). It's easier to manipulate a set of files or one single file. To explore this app,
start wth the 2 interfaces 'JobInterface' (and its implementation FileJob) and 'Media' (and its 2 implementations MediaList and MediaFile).

## Todo
Calling external software uses the Process component from Symfony. Since those softwares could change, it can break this CLI.
I think a more abstract creation of process is needed.
Perhaps an Abstract Factory design pattern with an injected external config could do the job...