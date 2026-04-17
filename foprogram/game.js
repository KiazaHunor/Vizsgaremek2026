
    let activeTournament = null;

const token = localStorage.getItem("token");

if (!token) {
  alert("A draft használatához be kell jelentkezned!");
  window.location.href = "../bejelentkezo/frontend/index.html";
}


    let selectedSwapSlot = null;
    let selectedSlot = null;



    const formations = {
      "4-3-3": [
        ["LW", "CF", "RW"],
        ["CM", "CAM", "CM"],
        ["LB", "CB", "CB", "RB"],
        ["GK"]
      ],
      "4-4-2": [
        ["CF", "CF"],
        ["LM", "CM", "CM", "RM"],
        ["LB", "CB", "CB", "RB"],
        ["GK"]
      ],
      "4-3-1-2": [
        ["CF", "CF"],
        ["CAM"],
        ["CM", "CM", "CM"],
        ["LB", "CB", "CB", "RB"],
        ["GK"]
      ],
      "4-1-2-1-2": [
        ["CF", "CF"],
        ["CAM"],
        ["CM", "CM"],
        ["CDM"],
        ["LB", "CB", "CB", "RB"],
        ["GK"]
      ],
      "4-5-1": [
        ["CF"],
        ["LM", "CM", "CDM", "CM", "RM"],
        ["LB", "CB", "CB", "RB"],
        ["GK"]
      ],
      "4-2-4": [
        ["LM","CF","CF","RM"],
        ["CM", "CM"],
        ["LB", "CB", "CB", "RB"],
        ["GK"]
      ],
      "4-1-4-1": [
        ["CF"],
        ["LM","CAM","CAM","RM"],
        ["CDM"],
        ["LB", "CB", "CB", "RB"],
        ["GK"]
      ],
      "3-4-3": [
        ["LW", "CF", "RW"],
        ["LM", "CM", "CM", "RM"],
        ["CB", "CB", "CB"],
        ["GK"]
      ],
      "3-4-2-1": [
        ["CF"],
        ["CAM","CAM"],
        ["LM", "CM", "CM", "RM"],
        ["CB", "CB", "CB"],
        ["GK"]
      ],
      "3-4-1-2": [
        ["CF", "CF"],
        ["CAM"],
        ["LM", "CM", "CM", "RM"],
        ["CB", "CB", "CB"],
        ["GK"]
      ],
      "3-1-4-2": [
        ["CF", "CF"],
        ["LM", "CM", "CM", "RM"],
        ["CDM"],
        ["CB", "CB", "CB"],
        ["GK"]
      ],
      "3-5-2": [
        ["CF", "CF"],
        ["LM", "CM", "CAM", "CM", "RM"],
        ["CB", "CB", "CB"],
        ["GK"]
      ],
      "5-4-1": [
        ["CF"],
        ["LM", "CM", "CM", "RM"],
        ["LB", "CB", "CB", "CB", "RB"],
        ["GK"]
      ],
      "5-3-2": [
        ["CF","CF"],
        ["CM", "CM", "CM"],
        ["LB", "CB", "CB", "CB", "RB"],
        ["GK"]
      ],
      "5-2-2-1": [
        ["CF"],
        ["CAM", "CAM"],
        ["CM", "CM"],
        ["LB", "CB", "CB", "CB", "RB"],
        ["GK"]
      ],
      "5-2-1-2": [
        ["CF","CF"],
        ["CAM"],
        ["CM", "CM"],
        ["LB", "CB", "CB", "CB", "RB"],
        ["GK"]
      ],
    };

    const extraSlots = [
      "CSERE", "CSERE", "CSERE", "CSERE", "CSERE", "CSERE",
      "CSERE", "TART", "TART", "TART", "TART", "TART"
    ];

    const positionCompatibility = {
      CF:  { CF: 1, CAM: 0.7, LW: 0.7, RW: 0.7, CM: 0.4, LM: 0.4, RM: 0.4, CDM: 0.1, CB: 0.1, LB: 0.1, RB: 0.1, GK: 0 },
      LW:  { LW: 1, LM: 0.7, CAM: 0.7, RW: 0.7, CF: 0.4, CM: 0.4, CDM: 0.1, LB: 0.1, RB: 0.1, CB: 0.1, GK: 0 },
      RW:  { RW: 1, RM: 0.7, CAM: 0.7, LW: 0.7, CF: 0.4, CM: 0.4, CDM: 0.1, LB: 0.1, RB: 0.1, CB: 0.1, GK: 0 },
      LM:  { LM: 1, LW: 0.7, CM: 0.7, RM: 0.7, CAM: 0.4, LB: 0.4, CDM: 0.4, CF: 0.1, RW: 0.1, RB: 0.1, CB: 0.1, GK: 0 },
      RM:  { RM: 1, RW: 0.7, CM: 0.7, LM: 0.7, CAM: 0.4, RB: 0.4, CDM: 0.4, CF: 0.1, LW: 0.1, LB: 0.1, CB: 0.1, GK: 0 },
      CM:  { CM: 1, CAM: 0.7, CDM: 0.7, LM: 0.7, RM: 0.7, CF: 0.4, LW: 0.4, RW: 0.4, CB: 0.4, LB: 0.1, RB: 0.1, GK: 0 },
      CAM: { CAM: 1, CM: 0.7, CF: 0.7, LW: 0.7, RW: 0.7, LM: 0.4, RM: 0.4, CDM: 0.4, CB: 0.1, LB: 0.1, RB: 0.1, GK: 0 },
      CDM: { CDM: 1, CM: 0.7, CB: 0.7, CAM: 0.4, LB: 0.4, RB: 0.4, LM: 0.1, RM: 0.1, CF: 0.1, LW: 0.1, RW: 0.1, GK: 0 },
      CB:  { CB: 1, CDM: 0.7, LB: 0.7, RB: 0.7, CM: 0.4, CAM: 0.1, LM: 0.1, RM: 0.1, CF: 0.1, LW: 0.1, RW: 0.1, GK: 0 },
      LB:  { LB: 1, CB: 0.7, LM: 0.7, CDM: 0.4, RB: 0.4, CM: 0.1, CAM: 0.1, LW: 0.1, CF: 0.1, RW: 0.1, GK: 0 },
      RB:  { RB: 1, CB: 0.7, RM: 0.7, CDM: 0.4, LB: 0.4, CM: 0.1, CAM: 0.1, RW: 0.1, CF: 0.1, LW: 0.1, GK: 0 },
      GK:  { GK: 1 }
    };

    function normalizePosition(pos) {
      const value = (pos || "").trim().toLowerCase();

      const map = {
        "cf": "CF",
        "centre-forward": "CF",
        "center-forward": "CF",
        "lw": "LW",
        "left winger": "LW",
        "rw": "RW",
        "right winger": "RW",
        "lm": "LM",
        "left midfield": "LM",
        "rm": "RM",
        "right midfield": "RM",
        "cm": "CM",
        "central midfield": "CM",
        "cam": "CAM",
        "attacking midfield": "CAM",
        "central attacking midfield": "CAM",
        "cdm": "CDM",
        "defensive midfield": "CDM",
        "central defensive midfield": "CDM",
        "cb": "CB",
        "centre-back": "CB",
        "center-back": "CB",
        "lb": "LB",
        "left-back": "LB",
        "rb": "RB",
        "right-back": "RB",
        "gk": "GK",
        "goalkeeper": "GK"
      };

      return map[value] || (pos || "").toUpperCase();
    }

    function getColClass(count) {
      switch (count) {
        case 1: return "col-4";
        case 2: return "col-4";
        case 3: return "col-4";
        case 4: return "col-3";
        case 5: return "col";
        default: return "col";
      }
    }

    function generateFormationHTML(rows) {
      return rows.map((row, rowIndex) => {
        const colClass = getColClass(row.length);
        const isLastRow = rowIndex === rows.length - 1;

        return `
          <div class="row justify-content-center g-3 formation-row ${isLastRow ? "" : "mb-4"}">
            ${row.map(position => `
              <div class="${colClass}">
                <div
                  class="player"
                  data-slot-position="${position}"
                  onclick="loadPlayers('${position}', this)"
                >${position}</div>
              </div>
            `).join("")}
          </div>
        `;
      }).join("");
    }

    function generateExtras() {
      const container = document.getElementById("extras-container");

      container.innerHTML = `
        <div class="row g-3 justify-content-center">
          ${extraSlots.map(position => `
            <div class="col-6 col-sm-4 col-md-3 col-lg-2 extra-slot-wrap">
              <div class="extra-player" data-slot-position="${position}" onclick="loadPlayers('${position}', this)">${position}</div>
            </div>
          `).join("")}
        </div>
      `;
    }

    function renderDraftCard(player, imageSrc, chem = 0) {
      return `
        <div class="draft-card" style="position:relative;">
          <div class="chem-dots">
            <div class="chem-dot ${chem >= 1 ? 'active' : ''}"></div>
            <div class="chem-dot ${chem >= 2 ? 'active' : ''}"></div>
            <div class="chem-dot ${chem >= 3 ? 'active' : ''}"></div>
          </div>

          <div class="draft-card-top">
            <div class="draft-card-pos">${player.position}</div>
          </div>

          <div class="draft-card-team">${player.team}</div>

          <div class="draft-card-image">
            <img src="${imageSrc}" alt="${player.team}">
          </div>

          <div class="draft-card-nationality">${player.nationality}</div>
          <div class="draft-card-rating">RTG ${player.rating ?? 0}</div>
          <div class="draft-card-name">${player.name}</div>
        </div>
      `;
    }

    function loadFormation(formation) {
      document.getElementById("cim").innerText = "Állítsd össze a csapatodat";
      document.getElementById("formaciok").style.display = "none";

      const container = document.getElementById("formation-container");
      const scoreBox = document.getElementById("score-box");
      const rows = formations[formation];

      if (!rows) {
        container.innerHTML = `<div class="text-danger">Ismeretlen formáció.</div>`;
        return;
      }

      container.innerHTML = generateFormationHTML(rows);

      const players = container.querySelectorAll(".player");
      players.forEach((player, i) => {
        setTimeout(() => {
          player.classList.add("show");
        }, i * 180);
      });

      scoreBox.classList.remove("d-none");
      generateExtras();
      updateScoreDisplay();
    }

    function getUniquePlayers(players) {
      const seen = new Set();

      return players.filter(player => {
        const id = String(player.id);
        if (seen.has(id)) return false;
        seen.add(id);
        return true;
      });
    }

    function getSelectedPlayerIds(currentSlot = null) {
      const slots = document.querySelectorAll(".player.locked, .extra-player.locked");
      const ids = [];

      slots.forEach(slot => {
        if (!slot.dataset.playerId) return;
        if (currentSlot && slot === currentSlot) return;
        ids.push(String(slot.dataset.playerId));
      });

      return ids;
    }

    async function loadPlayers(position, element) {
      selectedSlot = element;

      try {
        const excludeIds = getSelectedPlayerIds(element);

        const response = await fetch(
        `get_players.php?position=${encodeURIComponent(position)}&exclude=${encodeURIComponent(excludeIds.join(","))}`,
        {
          headers: {
            Authorization: "Bearer " + token
          }
        }
      );

        const text = await response.text();
        const data = JSON.parse(text);

        const playerList = document.getElementById("player-list");
        playerList.innerHTML = "";

        if (!data.success) {
          playerList.innerHTML = `<div class="text-danger">${data.message}</div>`;
        } else {
          const uniquePlayers = getUniquePlayers(data.players || []);
          const alreadyUsedIds = new Set(getSelectedPlayerIds(element));

          const filteredPlayers = uniquePlayers.filter(player => {
            return !alreadyUsedIds.has(String(player.id));
          });

          if (filteredPlayers.length === 0) {
            playerList.innerHTML = `<div class="text-warning">Nincs több választható játékos erre a pozícióra.</div>`;
          } else {
            filteredPlayers.forEach(player => {
              const button = document.createElement("button");
              button.type = "button";
              button.className = "list-group-item list-group-item-action custom-player-item";

              const imageSrc = player.shirt_image ? player.shirt_image : "hatternelkul/default.png";
              const normalizedPos = normalizePosition(player.position);
              const rating = Number(player.rating || 0);

              button.innerHTML = `
                <div class="d-flex align-items-center gap-3 text-start">
                  <img
                    src="${imageSrc}"
                    alt="${player.team}"
                    style="width:50px; height:50px; object-fit:contain; border-radius:8px; border:1px solid #00ff9d; background:#0f141c;"
                  >
                  <div>
                    <strong>${player.name}</strong><br>
                    <small>${player.team}</small><br>
                    <small>${player.nationality}</small><br>
                    <small>${normalizedPos}</small><br>
                    <small>RTG: ${rating}</small>
                  </div>
                </div>
              `;

              button.onclick = function () {
                const slot = selectedSlot;
                const currentSelectedIds = new Set(getSelectedPlayerIds(slot));
                const playerId = String(player.id);

                if (currentSelectedIds.has(playerId)) {
                  alert("Ez a játékos már ki van választva.");
                  return;
                }

                slot.dataset.playerId = player.id;
                slot.dataset.position = normalizedPos;
                slot.dataset.team = (player.team || "").trim();
                slot.dataset.nationality = (player.nationality || "").trim();
                slot.dataset.playerName = (player.name || "").trim();
                slot.dataset.imageSrc = imageSrc;
                slot.dataset.rating = rating;

                slot.innerHTML = renderDraftCard({
                  name: player.name,
                  position: normalizedPos,
                  team: player.team,
                  nationality: player.nationality,
                  rating: rating
                }, imageSrc, 0);

                slot.classList.add("locked");
                slot.onclick = () => handleSlotClick(slot);

                const modalInstance = bootstrap.Modal.getInstance(document.getElementById("playerModal"));
                modalInstance.hide();

                updateScoreDisplay();
              };

              playerList.appendChild(button);
            });
          }
        }

        const modal = new bootstrap.Modal(document.getElementById("playerModal"), {
          backdrop: "static",
          keyboard: false
        });
        modal.show();

      } catch (error) {
        console.error("Hiba:", error);
        alert("Hiba történt a játékosok betöltése közben.");
      }
    }

    function handleSlotClick(slot) {
      if (!slot.dataset.playerId) return;

      if (selectedSwapSlot === null) {
        selectedSwapSlot = slot;
        slot.classList.add("selected-swap");
        return;
      }

      if (selectedSwapSlot === slot) {
        slot.classList.remove("selected-swap");
        selectedSwapSlot = null;
        return;
      }

      const firstSlot = selectedSwapSlot;
      firstSlot.classList.remove("selected-swap");

      swapSlots(firstSlot, slot);
      selectedSwapSlot = null;
    }

    function swapSlots(slot1, slot2) {
      const tempData = {
        innerHTML: slot1.innerHTML,
        playerId: slot1.dataset.playerId || "",
        position: slot1.dataset.position || "",
        team: slot1.dataset.team || "",
        nationality: slot1.dataset.nationality || "",
        playerName: slot1.dataset.playerName || "",
        imageSrc: slot1.dataset.imageSrc || "",
        rating: slot1.dataset.rating || "0"
      };

      slot1.innerHTML = slot2.innerHTML;
      slot1.dataset.playerId = slot2.dataset.playerId || "";
      slot1.dataset.position = slot2.dataset.position || "";
      slot1.dataset.team = slot2.dataset.team || "";
      slot1.dataset.nationality = slot2.dataset.nationality || "";
      slot1.dataset.playerName = slot2.dataset.playerName || "";
      slot1.dataset.imageSrc = slot2.dataset.imageSrc || "";
      slot1.dataset.rating = slot2.dataset.rating || "0";

      slot2.innerHTML = tempData.innerHTML;
      slot2.dataset.playerId = tempData.playerId;
      slot2.dataset.position = tempData.position;
      slot2.dataset.team = tempData.team;
      slot2.dataset.nationality = tempData.nationality;
      slot2.dataset.playerName = tempData.playerName;
      slot2.dataset.imageSrc = tempData.imageSrc;
      slot2.dataset.rating = tempData.rating;

      slot1.onclick = () => handleSlotClick(slot1);
      slot2.onclick = () => handleSlotClick(slot2);

      updateScoreDisplay();
    }

    function getStartingSlots() {
      return Array.from(document.querySelectorAll(".formation-container .player.locked"))
        .filter(slot => slot.dataset.playerId);
    }

    function countBy(list, keyFn) {
      const map = {};

      list.forEach(item => {
        const key = keyFn(item);
        if (!key) return;
        map[key] = (map[key] || 0) + 1;
      });

      return map;
    }

    function getTeamChemPoints(count) {
      if (count >= 6) return 3;
      if (count >= 4) return 2;
      if (count >= 2) return 1;
      return 0;
    }

    function getNationChemPoints(count) {
      if (count >= 8) return 2;
      if (count >= 4) return 1;
      return 0;
    }

    function getPositionMultiplier(originalPosition, slotPosition) {
      const from = normalizePosition(originalPosition);
      const to = normalizePosition(slotPosition);

      if (!from || !to) return 0;
      if (from === "GK" && to !== "GK") return 0;
      if (from !== "GK" && to === "GK") return 0;

      return positionCompatibility[from]?.[to] ?? 0;
    }

    function calculateTeamScore() {
      const slots = getStartingSlots();

      if (slots.length === 0) {
        return {
          players: [],
          totalChem: 0,
          chemistryScore: 0,
          averageRatingScore: 0,
          finalScore: 0
        };
      }

      const players = slots.map(slot => ({
        slot,
        slotPosition: slot.dataset.slotPosition || "",
        originalPosition: slot.dataset.position || "",
        team: (slot.dataset.team || "").trim(),
        nationality: (slot.dataset.nationality || "").trim(),
        teamKey: (slot.dataset.team || "").trim().toLowerCase(),
        nationalityKey: (slot.dataset.nationality || "").trim().toLowerCase(),
        id: slot.dataset.playerId || "",
        rating: Number(slot.dataset.rating || 0),
        playerName: slot.dataset.playerName || "",
        imageSrc: slot.dataset.imageSrc || ""
      }));

      const teamCounts = countBy(players, p => p.teamKey);
      const nationCounts = countBy(players, p => p.nationalityKey);

      const resultPlayers = players.map(p => {
        const teamPoints = getTeamChemPoints(teamCounts[p.teamKey] || 0);
        const nationPoints = getNationChemPoints(nationCounts[p.nationalityKey] || 0);

        const baseChem = Math.min(3, teamPoints + nationPoints);
        const multiplier = getPositionMultiplier(p.originalPosition, p.slotPosition);
        const finalChem = Math.max(0, Math.min(3, Math.round(baseChem * multiplier)));

        return {
          ...p,
          teamPoints,
          nationPoints,
          baseChem,
          multiplier,
          finalChem,
          ratingScore: p.rating * 10
        };
      });

      const totalChem = resultPlayers.reduce((sum, p) => sum + p.finalChem, 0);
      const chemistryScore = totalChem * 100;

      const totalRatingScore = resultPlayers.reduce((sum, p) => sum + p.ratingScore, 0);
      const averageRatingScore = Math.round(totalRatingScore / resultPlayers.length);

      const finalScore = chemistryScore + averageRatingScore;

      return {
        players: resultPlayers,
        totalChem,
        chemistryScore,
        averageRatingScore,
        finalScore
      };
    }

    function updateScoreDisplay() {
      const result = calculateTeamScore();
      const box = document.getElementById("score-box");

      if (!box) return;

      box.innerHTML = `
        Chemistry: ${result.chemistryScore} / 3300
        <br>
        <small>Rating átlag pont: ${result.averageRatingScore}</small>
        <br>
        <span>Fő pont: ${result.finalScore}</span>
      `;

      result.players.forEach(p => {
        p.slot.innerHTML = renderDraftCard({
          name: p.playerName,
          position: p.originalPosition,
          team: p.team,
          nationality: p.nationality,
          rating: p.rating
        }, p.imageSrc, p.finalChem);

        p.slot.onclick = () => handleSlotClick(p.slot);
      });
    }

    function logChemistryDetails() {
      const result = calculateTeamScore();
      console.table(
        result.players.map(p => ({
          slot: p.slotPosition,
          original: p.originalPosition,
          team: p.team,
          nationality: p.nationality,
          rating: p.rating,
          ratingScore: p.ratingScore,
          teamPoints: p.teamPoints,
          nationPoints: p.nationPoints,
          baseChem: p.baseChem,
          multiplier: p.multiplier,
          finalChem: p.finalChem
        }))
      );
    }
 
    function showRandomFormationButtons(count = 5) {
  const buttons = Array.from(document.querySelectorAll("#formaciok .btn-formation"));

  buttons.forEach(button => {
    button.style.display = "none";
  });

  const shuffled = [...buttons].sort(() => Math.random() - 0.5);
  const selected = shuffled.slice(0, count);

  selected.forEach(button => {
    button.style.display = "inline-block";
  });
}



function getAllMainSlots() {
  return Array.from(document.querySelectorAll(".formation-container .player"));
}

function isDraftComplete() {
  const mainSlots = getAllMainSlots();
  if (mainSlots.length === 0) return false;

  return mainSlots.every(slot => slot.dataset.playerId);
}

function handleDraftSummary() {
  if (!isDraftComplete()) {
    alert("Még nem választottál ki minden slotra játékost!");
    return;
  }

  showSummaryCard();
}

function showSummaryCard() {
  const result = calculateTeamScore();

  const chemistryEl = document.getElementById("summary-chemistry");
  const ratingEl = document.getElementById("summary-rating");
  const finalEl = document.getElementById("summary-final");
  const overlay = document.getElementById("draft-summary-overlay");

  if (!chemistryEl || !ratingEl || !finalEl || !overlay) return;

  chemistryEl.textContent = `${result.chemistryScore} / 3300`;
  ratingEl.textContent = result.averageRatingScore;
  finalEl.textContent = result.finalScore;

  overlay.classList.remove("d-none");
}

function closeSummaryCard() {
  const overlay = document.getElementById("draft-summary-overlay");
  if (!overlay) return;

  overlay.classList.add("d-none");
}

function startGame() {
  
}




function loadActiveTournament() {
  fetch("get_active_tournament.php", {
    headers: {
      Authorization: "Bearer " + token
    }
  })
    .then(response => response.json())
    .then(data => {
      const box = document.getElementById("tournament-box");
      const info = document.getElementById("tournament-info");
      const btn = document.getElementById("join-tournament-btn");

      if (!box || !info || !btn) return;

      box.classList.remove("d-none");

      if (!data.success || !data.tournament) {
        activeTournament = null;
        info.innerHTML = "Jelenleg nincs aktív bajnokság.";
        btn.disabled = true;
        return;
      }

      activeTournament = data.tournament;

      info.innerHTML = `
        <strong>${data.tournament.name}</strong><br>
        Nevezési határidő: ${data.tournament.entry_deadline}
      `;

      btn.disabled = false;
    })
    .catch(error => {
      console.error("Aktív bajnokság betöltési hiba:", error);
    });
}

function joinTournament() {
  if (!activeTournament) {
    alert("Nincs aktív bajnokság.");
    return;
  }

  const teamNameInput = document.getElementById("team-name-input");
  const teamName = (teamNameInput.value || "").trim();

  if (teamName.length < 3) {
    alert("Adj meg egy csapatnevet!");
    return;
  }

  const slots = getStartingSlots();
  if (slots.length < 11) {
    alert("Előbb rakd össze a kezdő 11-et.");
    return;
  }

  const result = calculateTeamScore();

  const payload = {
    tournament_id: Number(activeTournament.id),
    team_name: teamName,
    chemistry_score: result.chemistryScore,
    rating_avg_score: result.averageRatingScore,
    final_score: result.finalScore
  };

  fetch("submit_tournament_entry.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      Authorization: "Bearer " + token
    },
    body: JSON.stringify(payload)
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert("Sikeres nevezés!");
        document.getElementById("join-tournament-btn").disabled = true;

        document.getElementById("team-name-input").value = "";
      } else {
        alert(data.message || "Hiba történt.");
      }
    })
    .catch(error => {
      console.error(error);
      alert("Hiba történt a nevezés közben.");
    });
}



document.addEventListener("DOMContentLoaded", function () {
  showRandomFormationButtons(5);
  loadActiveTournament();
});