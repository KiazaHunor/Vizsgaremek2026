from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.service import Service
from webdriver_manager.chrome import ChromeDriverManager
import csv
import time

def get_player_basic_data(player_id, driver):
    url = f"https://hlsz.hu/{player_id}"
    driver.get(url)
    time.sleep(2)  # JS betöltés

    try:
        element = driver.find_element(By.ID, "playerData")
        return element.text.strip()
    except:
        return "Nincs adat"

def scrape_multiple_players_to_csv(player_ids):
    options = webdriver.ChromeOptions()
    options.add_argument('--headless')
    options.add_argument('--disable-gpu')

    driver = webdriver.Chrome(
        service=Service(ChromeDriverManager().install()),
        options=options
    )

    filename = "jatekos_alapadatok.csv"

    with open(filename, "w", newline="", encoding="utf-8-sig") as file:
        writer = csv.writer(file)
        writer.writerow(["Játékos ID", "Alapadatok"])

        for player_id in player_ids:
            print(f"🔄 Lekérés: {player_id}")
            data = get_player_basic_data(player_id, driver)
            writer.writerow([player_id, data])

    driver.quit()
    print(f"\n✅ MINDEN JÁTÉKOS ADATA ELMENTVE IDE: {filename}")

# ===== FUTTATÁS =====
if __name__ == "__main__":

    print("Add meg a játékosokat ID alapján!")
    print("Példa: 1994-01-25/varga-barnabas")
    print("Ha végeztél, csak ENTER-t nyomj.\n")

    player_ids = []

    while True:
        pid = input("Játékos ID: ").strip()
        if not pid:
            break
        player_ids.append(pid)

    if not player_ids:
        print("❌ Nem adtál meg egyetlen játékost sem!")
    else:
        scrape_multiple_players_to_csv(player_ids)
