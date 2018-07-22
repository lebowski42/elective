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

für die Installation müssen 


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
 
