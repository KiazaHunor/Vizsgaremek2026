let selectedAnswer = "";
let currentQuestionId = 0;
let currentUserId = 0;

function getLoggedInUser() {
  const token = localStorage.getItem("token");

  if (!token) {
    document.getElementById("quiz-message").innerHTML =
      '<div class="alert alert-warning">A kvízhez be kell jelentkezned!</div>';
    document.getElementById("submit-btn").disabled = true;
    return;
  }

  fetch("/bejelentkezo/backend/api/profile.php", {
    headers: {
      Authorization: "Bearer " + token
    }
  })
    .then(res => res.json())
    .then(data => {
      if (!data.success) throw new Error();

      currentUserId = data.user.id;
      loadQuestion();
    })
    .catch(() => {
      document.getElementById("quiz-message").innerHTML =
        '<div class="alert alert-danger">Nem sikerült lekérni a felhasználót.</div>';
    });
}

function loadQuestion() {
  fetch("/api/get_today_question.php")
    .then(res => res.json())
    .then(data => {
      if (!data.success) {
        document.getElementById("quiz-message").innerHTML = data.error;
        return;
      }

      if (data.already_answered) {
        document.getElementById("quiz-message").innerHTML =
          '<div class="alert alert-info">Ma már kitöltötted a kvízt.</div>';
        document.getElementById("submit-btn").disabled = true;
      }

      const q = data.question;
      currentQuestionId = q.id;
      document.getElementById("question-text").innerText = q.text;

      const answersDiv = document.getElementById("answers");
      answersDiv.innerHTML = "";

      [q.answerA, q.answerB, q.answerC, q.answerD].forEach(answer => {
        const btn = document.createElement("button");
        btn.className = "answer-btn";
        btn.innerText = answer;

        btn.onclick = function () {
          selectedAnswer = answer;

          document.querySelectorAll(".answer-btn").forEach(b =>
            b.classList.remove("selected")
          );

          btn.classList.add("selected");
        };

        answersDiv.appendChild(btn);
      });
    })
    .catch(() => {
      document.getElementById("quiz-message").innerHTML =
        '<div class="alert alert-danger">Hiba történt a kérdés betöltésekor.</div>';
    });
}

function submitAnswer() {
  if (!selectedAnswer) return alert("Válassz!");

  fetch("/api/submit_answer.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      user_id: currentUserId,
      question_id: currentQuestionId,
      selected_answer: selectedAnswer
    })
  })
    .then(res => res.json())
    .then(data => {

      if (data.already_answered) {
        document.getElementById("result").innerHTML =
          '<div class="alert alert-warning">Ma már válaszoltál, nem lehet többször!</div>';
        document.getElementById("submit-btn").disabled = true;
        return;
      }

      if (!data.success) {
        document.getElementById("result").innerHTML =
          '<div class="alert alert-danger">' + (data.error || "Hiba történt") + '</div>';
        return;
      }

      document.getElementById("result").innerHTML =
        data.is_correct
          ? `<div class="alert alert-success">✅ Helyes! Sorozat: ${data.current_streak}</div>`
          : `<div class="alert alert-danger">❌ Rossz! Helyes: ${data.correct_answer}</div>`;

      document.getElementById("submit-btn").disabled = true;
    })
    .catch(() => {
      document.getElementById("result").innerHTML =
        '<div class="alert alert-danger">Hiba történt a válasz elküldésekor.</div>';
    });
}

getLoggedInUser();