# Pimcore Challenge - Football Clubs

Mit dieser Applikation kann man selber Fußballvereine und dessen Spieler anlegen und anzeigen lassen.

Diese Challenge wurde in einer Docker-Umgebung erstellt. Die Schritte unter [Docker](#docker) ausführen und schon kann man das Projekt einsehen und eigene Vereine und Spieler angeben.

## Docker Installation
### Wichtig

* Dein User muss die Rechte haben docker-Befehle ausführen zu können (direkt oder mit sudo).
* Du musst docker compose installiert haben.
* Dein User muss die Rechte haben die Datei-Rechte zu ändern (direkt oder mit sudo).

### Die Schritte zum installieren

1. Das Projekt als zip downloaden und entpacken
   
2. In das neue entpackte Projekt reingehen. 
   **Achtung:** Sehr wahscheinlich wird der Projekt-Ordner `pimcore_challenge-main` heißen, solange der Ordner nicht umbenannt worden ist.
   ```
   cd pimcore_challenge-main
   ```

3. Das Projekt initialisieren: 
   ```
   docker run -u `id -u`:`id -g` --rm -v `pwd`:/var/www/html pimcore/pimcore:php8.2-latest composer create-project .
   ``` 

4. Den Befehl ``echo `id -u`:`id -g`` ausführen um den local user und group id zu erfahren.
   - Den `docker-compose.yaml` in einen Editor ausführen und falls nötig die user & group id bei `user: '1000:1000'`überschreiben 
  
5. Die Services starten mit: 
   ```
   docker compose up -d
   ```

6. Final dann Pimcore installieren und die Datenbank initialisieren mit:
   ```
   docker compose exec php vendor/bin/pimcore-install --mysql-host-socket=db --mysql-username=pimcore --mysql-password=pimcore --mysql-database=pimcore
   ```
   Die Namen für den Admin User und Passwort für die Pimcore-Installation kann man frei eingeben.

7. Fertig? Dann kannst du Pimcore nun besuchen!
   Frontend: [http://localhost](http://localhost)
   Admin Interface: [http://localhost/admin](http://localhost/admin)
   - Bitte nicht den Admin User-Name und Passwort vergessen!

### Wie lege ich schon die ersten Fußballvereine/Spieler an?
1. Gehe zu [http://localhost/admin](http://localhost/admin)
2. Unter Data Objects erstelle jeweils zwei Ordner. `footballclubs` & `players` zum Beispiel.
3. Mit Rechts-Klick klickst du auf einen der erstellten Ordner aus und erstellst einen neuen Data Object. Z.B. in dem Ordner `footballclubs` wählst du hier den Data Object `footballclub` und benennst ihm nach einem bestimmten Fußballverein.
4. Danach wählst du den neu erstellten Data Object und vervollständigst die Daten.