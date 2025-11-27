import requests
from bs4 import BeautifulSoup
import csv
import re

def scrape_mlsz_team(url):
    """
    MLSZ csapat adatlapjának letöltése és játékosok neveinek kinyerése
    
    Args:
        url: A csapat adatlapjának URL-je (pl: https://adatbank.mlsz.hu/club/65/0/31362/15/307004.html)
    """
    
    # Információ kiírása a konzolra, hogy lássuk mi történik
    print(f"Adatok letöltése: {url}")
    
    # ===== 1. LÉPÉS: Weboldal letöltése =====
    # A requests.get() letölti a weboldal HTML kódját
    response = requests.get(url)
    
    # Beállítjuk a magyar karakterek helyes megjelenítését (ékezetek)
    response.encoding = 'utf-8'
    
    # A BeautifulSoup segít feldolgozni a HTML kódot
    # Ez lehetővé teszi, hogy könnyen keressünk elemeket aHTML-ben
    soup = BeautifulSoup(response.text, 'html.parser')
    
    # ===== 2. LÉPÉS: Csapat nevének megkeresése =====
    # Megkeressük a h1 címsort, ami 'container_title' osztályú
    # Ez tartalmazza a csapat nevét (pl. "DVSC")
    team_name_element = soup.find('h1', class_='container_title')
    
    # Ha megtaláltuk az elemet, kivesszük a szöveget és eltávolítjuk a felesleges szóközöket
    # Ha nem találtuk meg, akkor "Unknown" lesz a név
    team_name = team_name_element.text.strip() if team_name_element else "Unknown"
    
    # Kiírjuk a csapat nevét a konzolra
    print(f"Csapat: {team_name}")
    
    # ===== 3. LÉPÉS: Játékosok táblázatának megkeresése =====
    # A játékosok egy tbody HTML elemben vannak, aminek az id-ja 'teamPlayers'
    players_table = soup.find('tbody', id='teamPlayers')
    
    # Ha nem találjuk a táblázatot, hibaüzenet és kilépés
    if not players_table:
        print("Nem található játékoslista!")
        return
    
    # ===== 4. LÉPÉS: Játékosok neveinek összegyűjtése =====
    # Létrehozunk egy üres listát, ahova a neveket gyűjtjük
    players_names = []
    
    # Végigmegyünk a táblázat minden során (tr = table row)
    for row in players_table.find_all('tr'):
        try:
            # ===== 4.1: Játékos nevének megkeresése =====
            # Minden játékos nevénél van egy link, ami a /player/SZÁM.html mintára illeszkedik
            # A re.compile() reguláris kifejezést használ a minta illesztésére
            name_link = row.find('a', href=re.compile(r'/player/\d+\.html'))
            
            # Ha nem találtunk linket ebben a sorban, akkor ugorjuk át
            if not name_link:
                continue
            
            # ===== 4.2: Név kinyerése =====
            # A link-en belül van egy span elem 'playerName' osztállyal
            # Ez tartalmazza a játékos nevét
            player_name_element = name_link.find('span', class_='playerName')
            
            # Kinyerjük a szöveget és eltávolítjuk a felesleges szóközöket
            player_name = player_name_element.text.strip()
            
            # ===== 4.3: Név hozzáadása a listához =====
            # A játékos nevét hozzáadjuk a players_names listához
            players_names.append(player_name)
            
        except Exception as e:
            # Ha bármilyen hiba történik egy játékos feldolgozásakor,
            # kiírjuk a hibát és folytatjuk a következő játékossal
            print(f"Hiba egy játékos feldolgozásakor: {e}")
            continue
    
    # ===== 5. LÉPÉS: CSV fájl létrehozása =====
    # A fájl neve a csapat nevéből készül, szóközöket aláhúzással helyettesítjük
    # Pl. "DVSC" -> "DVSC_jatekosok.csv"
    filename = f"{team_name.replace(' ', '_')}_jatekosok.csv"
    
    # Megnyitjuk a CSV fájlt írásra
    # 'w' = write (írás), newline='' = ne legyen extra üres sor
    # encoding='utf-8-sig' = magyar ékezetek helyes mentése Excel-hez
    with open(filename, 'w', newline='', encoding='utf-8-sig') as csvfile:
        
        # ===== 5.1: CSV író létrehozása =====
        # A csv.writer() segít szép, formázott CSV fájlt készíteni
        writer = csv.writer(csvfile)
        
        # ===== 5.2: Fejléc írása =====
        # Az első sor a fejléc, ami megmondja, hogy mi van az oszlopban
        writer.writerow(['Játékos neve'])
        
        # ===== 5.3: Játékosok neveinek írása =====
        # Végigmegyünk minden játékos nevén
        for name in players_names:
            # Minden nevet külön sorba írunk
            writer.writerow([name])
    
    # ===== 6. LÉPÉS: Sikeres mentés jelzése =====
    print(f"\n✅ Sikeres mentés: {filename}")
    print(f"📊 Összesen {len(players_names)} játékos exportálva")
    
    # Visszaadjuk a neveket, ha szükség lenne rájuk később
    return players_names


# ===== PROGRAM INDÍTÁSA =====
# Ez a rész akkor fut le, amikor közvetlenül futtatjuk a Python fájlt
if __name__ == "__main__":
    
    # ===== Felhasználói bemenet =====
    # Bekérjük a csapat URL-jét a felhasználótól
    # A strip() eltávolítja a felesleges szóközöket
    team_url = input("Add meg a csapat URL-jét: ").strip()
    
    # ===== Alapértelmezett URL használata =====
    # Ha a felhasználó nem adott meg URL-t (csak ENTER-t nyomott),
    # akkor használjunk egy példa URL-t (DVSC)
    if not team_url:
        team_url = "https://adatbank.mlsz.hu/club/65/0/31362/15/307004.html"
        print(f"Alapértelmezett URL használata: {team_url}")
    
    # ===== Scraping futtatása =====
    # Meghívjuk a fő függvényt, ami elvégzi a tényleges munkát
    scrape_mlsz_team(team_url)