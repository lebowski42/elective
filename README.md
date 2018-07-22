# elective

__elective__ ist eine Webanwendung um Projektkurse bzw Wahlkurse zu verwalten

## Kurzübersicht

**Lehrer** können einen Kurs anlegen, ihn beschreiben und weitere Lehrkräfte angeben, die sich an dem Kurs beteiligen.

**Schüler** können aus einer Liste von Angeboten (z. B. Projektkurse, Wahlfächern, Zusatzkursen) ihre favorisierten Kurse wählen. Dabei können sie angeben, was ihr Erstwunsch, Zweitwunsch, usw. ist.

**Administratoren** können sehen, welche Benutzer noch keine Angaben gemacht haben und sich Statistiken zur Wahl anzeigen lassen. Es können verschiedene Listen gedruckt werden (Kursübersicht, Kurslisten) und ein Algorithmus berechnet aufgrund der abgegebenen Wahlen und Kursgrößen eine ideale Verteilung der Schüler auf die Kurse

# Installation
Voraussetzungen für die Installation sind
- Webserver mit php >5.4
- Datenbank (am Besten MySQL oder MariaDB, sqlite und Microsoft SQLServer sind nicht getestet)

Lade das neuste [zip-Archiv](https://github.com/lebowski42/elective/archive/master.zip) herunter und entpacke es in deinem Webspace.

Lege eine Datenbank an. Du benötigst den Datenbankhost (alsodie Adresse für deine Datenbank), den Datenbanknamen und das zugehörige Passwort.

Öffne nun die Datei ... und trage unter den Punkten .. die werte ein.

## .htaccess und ssl-Zertifikat


## Datenbank anlegen
Die Datenbank wird durch den Aufruf von angelegt. Damit das Datenbankschema angelegt werden kann, muss die Datenbank zwingend leer sein. 

Danach können Sie sich mit dem Benutzernamen *admin* und dem Passwort *password* einloggen.

## Konfiguration
....

# Lehrer
Melden Sie sich mit ihrem Benutzernamen und Kennwort ein.
Haben Sie sich zum ersten Mal angemeldet, können Sie entscheiden, ob Sie einen neuen Kurs erstellen wollen, oder ob Sie einem bestehenden Kurs als weitere ehrkraft beitreten wollen.
Gehören Sie schon einem Kurs an, können Sie die Angaben zu diesem Kurs bearbeiten oder den Kurs verlassen.

## Kurs anlegen oder bearbeiten
Auf dieser Seite können Sie folgende Angaben zum Kurs machen:
- *Titel*
- *Beschreibung des Kurses*
- ...


# Schüler
Schüler loggen sich mit ihrem Benutzernamen und Kennwort ein. Danach sieht man auf der linken Seite eine Auswahlliste mit allen angebotenen Kursen. Hier aus müssen so viele Kurse gewählt werden, wie in der Konfigurationsdatei mit den Optionen ˋminCourseToChooseˋ  und ˋmaCourseToCooseˋ angegeben wurde.
Die gewählten Kurse werden in der der rechten Auswahlliste angezeigt. Die Reihenfolge der Kurse gibt de Priorität der Wahl an. An oberster Stelle steht der Erstwunsch, an zweiter Stelle der Zweitwunsch, usw. 
Damit die Wahl übernommen wird, muss auf *Auswahl speichern* geklickt werden. 
> Hat einSchüler bereits eine Auswahl getätigt und loggt sich erneut ein, so sieht er die bereits gemachte Auswahl im rechten Auswahlfeld. Er kann jederzeit Änderungen vornehmen und diese erneut abspeichern.

** Nach dem Ende der Auswahlzeit **
Ist das Ende der Auswahlzeit erreicht(Konfigurationsoption ˋdeadlineStudentsCourseToChooseˋ) werden die Schüler auf eine Übersichtsseite weitergeleitet. Hier sehen sie ihre Auswahl und, falls eine schon eine Kurszuweisung erfolgt ist, welchem Kurs sie zugewiesen sind.











web app for students to choose electives


In every school students have to choose from different electives, e. g. second language, sciences or even project weeks. In all this cases hundred of students have to make a choice. They also have to make a alternative choice, if the first choice can't be fullfilled. 
_elective_ takes on this task: just give a username and password to your students, let same make their selections and then calculate a good distribution of course participation.



This app provides three main functionality.

1. __Teacher__ can create and enter informations for a course. Or they can join another course as a teamteacher.
2. __Students__ can select a course. They also can select more courses and order them by priority.
3.  __Admin__ : 
   * If the course size is limited, elective can calculate a good distribution of students 
   * You can generate a lot of lists: course description; course member; list of students forgot to select, username/password list.
   * csv-import of users, username/password lists sorted by class.
   
   
   
   
Usage

Demo
Installation

Requirement

Feature requested
 
