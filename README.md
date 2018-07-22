# elective

__elective__ ist eine Webanwendung um Projektkurse bzw Wahlkurse zu verwalten

## Kurzübersicht

**Lehrer** können einen Kurs anlegen, ihn beschreiben und weitere Lehrkräfte angeben, die sich an dem Kurs beteiligen.

**Schüler** können aus einer Liste von Angeboten (z. B. Projektkurse, Wahlfächern, Zusatzkursen) ihre favorisierten Kurse wählen. Dabei können sie angeben, was ihr Erstwunsch, Zweitwunsch, usw. ist.

**Administratoren** können sehen, welche Benutzer noch keine Angaben gemacht haben und sich Statistiken zur Wahl anzeigen lassen. Es können verschiedene Listen gedruckt werden (Kursübersicht, Kurslisten) und ein Algorithmus berechnet aufgrund der abgegebenen Wahlen und Kursgrößen eine ideale Verteilung der Schüler auf die Kurse

## Installation
Voraussetzungen für die Installation sind
- Webserver mit php >5.4
- Datenbank (am Besten MySQL oder MariaDB, sqlite und Microsoft SQLServer sind nicht getestet)

Lade das neuste [zip-Archiv](https://github.com/lebowski42/elective/archive/master.zip) herunter und entpacke es in deinem Webspace.

Lege eine Datenbank an. Du benötigst den Datenbankhost (alsodie Adresse für deine Datenbank), den Datenbanknamen und das zugehörige Passwort.

Öffne nun die Datei ... und trage unter den Punkten .. die werte ein.

### .htaccess und ssl-Zertifikat


### Datenbank anlegen
Die Datenbank wird durch den Aufruf von angelegt. Damit das Datenbankschema angelegt werden kann, muss die Datenbank zwingend leer sein. 

Danach können Sie sich mit dem Benutzernamen *admin* und dem Passwort *password* einloggen.

### Konfiguration
....
## Elective benutzen
### Lehrer
Melden Sie sich mit ihrem Benutzernamen und Kennwort ein.
Haben Sie sich zum ersten Mal angemeldet, können Sie entscheiden, ob Sie einen neuen Kurs erstellen wollen, oder ob Sie einem bestehenden Kurs als weitere ehrkraft beitreten wollen.
Gehören Sie schon einem Kurs an, können Sie die Angaben zu diesem Kurs bearbeiten oder den Kurs verlassen.

### Kurs anlegen oder bearbeiten
Auf dieser Seite können Sie folgende Angaben zum Kurs machen:
- *Titel*
- *Beschreibung des Kurses*
- *Zusätzliche Lehrkräfte*
- *maximale Anzahl an Schülern* (Diese Option kann vom Administrator auch deaktiviert worden sein)
- *Notizen*

Wählen Sie danach *Kurs speichern* um die Angaben zu übernehmen. Auf der Übersichtsseite wird Ihnen dann Ihr Kurs angezeigt.

### Kurs beitreten oder Kurs verlassen
Wollen Sie keinen eigenen Kurs anbieten, so können Sie auch einem bestehenden Kurs als zusätzliche Lehrkraft beitreten. Wählen Sie diesen Menüpunkt, erhalten Sie eine Liste mit allen Kursen. Sie können über den Menüpunkt *Kursbeschreibung anzeigen* alle Informationen des Kurses abrufen. Wählen Sie dann einen Kurs aus und wählen *Kurs beitreten*. 
Auf der Übersichtsseite wird Ihnen danach Ihr Kurs angezeigt. Sie können jetzt auch über den Button *Kurs bearbeiten* die Angaben zum Kurs ändern. Alternativ können Sie den Kurs auch wieder verlassen.

### Nach dem Ende der Bearbeitungszeit
Nach dem Ende der Bearbeitungszeiten werden Ihn nur noch Informationen zu Ihrem Kurs angezeigt.
Sollten Ihrem Kurs schon Schüler zugewiesen worden sein, sehen Sie auch die Teilnehmer Ihres Kurses.

### Schüler
Schüler loggen sich mit ihrem Benutzernamen und Kennwort ein. Danach sieht man auf der linken Seite eine Auswahlliste mit allen angebotenen Kursen. Hier aus müssen so viele Kurse gewählt werden, wie in der Konfigurationsdatei mit den Optionen ˋminCourseToChooseˋ  und ˋmaCourseToCooseˋ angegeben wurde.
Die gewählten Kurse werden in der der rechten Auswahlliste angezeigt. Die Reihenfolge der Kurse gibt de Priorität der Wahl an. An oberster Stelle steht der Erstwunsch, an zweiter Stelle der Zweitwunsch, usw. 
Damit die Wahl übernommen wird, muss auf *Auswahl speichern* geklickt werden. 
> Hat einSchüler bereits eine Auswahl getätigt und loggt sich erneut ein, so sieht er die bereits gemachte Auswahl im rechten Auswahlfeld. Er kann jederzeit Änderungen vornehmen und diese erneut abspeichern.

### Nach dem Ende der Auswahlzeit
Ist das Ende der Auswahlzeit erreicht(Konfigurationsoption ˋdeadlineStudentsCourseToChooseˋ) werden die Schüler auf eine Übersichtsseite weitergeleitet. Hier sehen sie ihre Auswahl und, falls eine schon eine Kurszuweisung erfolgt ist, welchem Kurs sie zugewiesen sind.


### Administratoren
Loggen Sie sich als Administrator ein, werden Sie automatisch auf das Adminpanel weitergeleitet. Hier können Sie verschiedene administrative Aufgaben ausführen:

* Benutzer anlegen (einzeln oder per Import z. B aus dem Schulverwaltungsprogramm)
* Statistiken und Übersichten über alle Benutzer, Kurse und Wahlen erhaten
* Kurse bearbeiten
* Schüler manuell Kursen zuweisen
* Automatisch Kurszusammensetzungen aus den Wahlen generieren
* Räume zuweisen
* verschiedene Listen und Übersichten drucken

## Benutzerverwaltung
### Benutzergruppen
*elective* kennt drei Benutzergruppen:
* *Students* (Schüler)
* *Teachers* (Lehrer)
* *Admin* (Administrator)

Jeder Benutzer muss Mitglied genau einer dieser drei Gruppen sein. Durch die Benutzergruppe wird festgelegt, welche Aktionen der Benutzer durchführe darf. 

### Benutzer anlegen 

Der erste Schritt nach der Installation ist das Anlegen neuer Benutzer. Wechsel dazu auf den Reiter **Benutzer** im Adminpanel. Es gibt die Möglichkeit Benutzer einzeln anzulegen oder mehrere neue Benutzer auf einmal zu importieren.

> **Info**
>Die Angabe zur Anrede ist nur für Lehrer wichtig. Bei Schülern wird sie nicht verwendet und ist nicht erforderlich.
>  Die eMail-Adresse wird von elective noch nicht verwendet. In einer späteren Versionen sollen über die eMail-Adresse aber Informationen verschickt werden. 

#### Benutzer einzeln hinzufügen
Im Abschnit **Benutzer bearbeiten** den Button *Benutzer hinzufügen*. Es öffnet sich ein Dialog in dem alle Angaben zu dem Benutzer gemacht werden können. Erforderlich sind lediglich die Angaben *Benutzername*, *Passwort*, *Anrede* und *Grupper*.
Nach Drücken des Button *Benutzer hinzufügen* wird der Benutzer in die Datenbank übernommen und in der Liste angezeigt.

#### Mehrere Benutzer auf einmal importieren
In Schulen und Universitäten müssen meistens hunderte Benutzer auf einmal importiert werden. Hierzu benötigt man eine Textdatei, in der zeilenweise die Informationen der Benutzer stehen. Die einzelnen Informationen müssen durch Kommata getrennt werden (eine sog- [csv-Datei](https://de.m.wikipedia.org/wiki/CSV_(Dateiformat))).
Solche Dateien kann man meisten einfach aus den Schulverwaltungsprogrammen exportieren. Außerdem kann man aus jedem Tabellenkalkulationsprogramm (z. B. LibreOffice Calc oder MS Excel) solche Dateien erzeugen.

##### Aufbau der csv-Datei
In der csv-Datei muss jede Spalte die Form 
``` 
Benutzername,Passwort,Nachname,Vorname,Anrede,Klasse,eMail
``` 

haben, also z. B. 
``` 
MaxMeier,geheim,Meier,Max,Herr,7a,max@meier.de
MajaMüller,strengGeheim,,,,9a,
``` 

Notwendig ist die Angabe des Benutzernamens und des Passwortes . Die Angabe der Klasse ist sinnvoll, um die Listen nachher zu strukturieren (Klassenlisten, etc.). Bei Lehrern wird über die Klasse angegeben, in welcher Klasse er Klassenlehrer ist.


Sollen nicht alle Angaben gemacht werden, müssen trotzdem die Kommata richtig gesetzt werden, wie in der zweiten Zeile gezeigt.

##### Erstellen einer csv-Datei mit SchILD-NRW und LibreOffice Calc

Das folgende Vorgehen zeigt das Erstellen einer solchen csv-Datei am Beispiel des Schulverwaltungsprogramm *SchILD", das an den meisten Schulen in NRW benutzt wird.

##### csv-Datei importieren

##### Passwortlisten drucken

### Übersicht und Listen aller Art

### Kurse verwalten

#Räume verwalten












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
 
