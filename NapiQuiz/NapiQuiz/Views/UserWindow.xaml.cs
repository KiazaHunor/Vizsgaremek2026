using System;
using System.Linq;
using System.Windows;
using System.Windows.Controls;
using NapiQuiz.Data;
using NapiQuiz.Models;

namespace NapiQuiz.Views
{
    public partial class UserWindow : Window
    {
        private Question? todayQuestion;

        public UserWindow()
        {
            InitializeComponent();
            LoadTodayQuestion();
        }

        private void LoadTodayQuestion()
        {
            using var db = new QuizDbContext();

            todayQuestion = db.Questions.FirstOrDefault(q => q.ActiveDate == DateTime.Today);

            if (todayQuestion == null)
            {
                QuestionTextBlock.Text = "Ma nincs elérhető kérdés.";
                DisableAnswerButtons();
                return;
            }

            QuestionTextBlock.Text = todayQuestion.Text;
            ButtonA.Content = todayQuestion.AnswerA;
            ButtonB.Content = todayQuestion.AnswerB;
            ButtonC.Content = todayQuestion.AnswerC;
            ButtonD.Content = todayQuestion.AnswerD;
        }

        private void Answer_Click(object sender, RoutedEventArgs e)
        {
            if (todayQuestion == null)
                return;

            string userName = UserNameTextBox.Text.Trim();

            if (string.IsNullOrWhiteSpace(userName))
            {
                MessageBox.Show("Adj meg felhasználónevet.");
                return;
            }

            using var db = new QuizDbContext();

            bool alreadyAnswered = db.UserAnswers.Any(a =>
                a.UserName == userName &&
                a.AnswerDate.Date == DateTime.Today);

            if (alreadyAnswered)
            {
                MessageBox.Show("Ma már válaszoltál.");
                DisableAnswerButtons();
                return;
            }

            var clickedButton = sender as Button;
            string selectedAnswer = clickedButton?.Content?.ToString() ?? "";

            bool isCorrect = selectedAnswer == todayQuestion.CorrectAnswer;

            var userAnswer = new UserAnswer
            {
                UserName = userName,
                QuestionId = todayQuestion.Id,
                SelectedAnswer = selectedAnswer,
                AnswerDate = DateTime.Today,
                IsCorrect = isCorrect
            };

            db.UserAnswers.Add(userAnswer);
            db.SaveChanges();

            if (selectedAnswer == todayQuestion.CorrectAnswer)
            {
                MessageBox.Show("Helyes válasz!");
            }
            else
            {
                MessageBox.Show($"Helytelen. A helyes válasz: {todayQuestion.CorrectAnswer}");
            }

            DisableAnswerButtons();
        }

        private void DisableAnswerButtons()
        {
            ButtonA.IsEnabled = false;
            ButtonB.IsEnabled = false;
            ButtonC.IsEnabled = false;
            ButtonD.IsEnabled = false;
        }
    }
}