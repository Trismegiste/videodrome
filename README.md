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
```
flo@spin5:~$ git clone https://github.com/Trismegiste/videodrome
flo@spin5:~$ composer.phar install
```

## Documentation

### List of available commands
```
flo@spin5:~$ php app.php list
```

### Check for installed softwares
```
flo@spin5:~$ php app.php system:check
```

### Conference
This command builds a mpeg4 movie with low framerate from 3 files :
* a LibreOffice Impress document
* a sound file with a recorded voice for example
* an Audacity marker file for timing each slide

```
flo@spin5:~$ php app.php conference:build presentation.odp myvoice.mp3 timecode.txt
```

### Advertisement
This command is almost the same as Conference except it generates an animated GIF. 
No sound nor timecode are required. Each frame has a default duration of 5 seconds (can be changed with option '--delay').

```
flo@spin5:~$ php app.php conference:gif --delay=10 --width=600 --height=400 presentation.odp
```

### Trailer
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

```
flo@spin5:~$ php app.php trailer:build ./clip ./slide ./svg epicmusic.ogg timecode.txt
```

Launch the command and wait. You need a lot of temporary disk space as it uses uncompressed video codec to ensure
the best quality before final encoding into mpeg4 format. The video is fully compatible with Youtube (n reencoding needed).

### Trailer, other commands
Each step of trailer building could be launch separately :
* Panning creations of pictures
* Cutting clips
* Overlay a set of transparent SVG
* Concatenation of a set of clips
* Muxing a sound into a video

## Internals
Each command use the same pattern for atomic conversion of one (or many) media file into one (or many) other media file.
It's loosely based of a Chain of Responsibilities design pattern. You can easily use the atomic component to create another movie builder.

This app is very strict and ensure no files are missing or lost in the process. 2 classes (MediaList and MediaFile) implement a
Composite Design Pattern (with only one level of children). It's easier to manipulate a set of files or one single file.