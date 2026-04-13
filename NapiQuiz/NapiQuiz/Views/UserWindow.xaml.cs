using System;
using System.Linq;
using System.Windows;
using System.Windows.Controls;
using System.Windows.Media;
using NapiQuiz.Data;
using NapiQuiz.Models;

namespace NapiQuiz.Views
{
    public partial class UserWindow : Window
    {
        private readonly User _loggedInUser;
        private Question? todayQuestion;

        public UserWindow(User loggedInUser)
        {
            InitializeComponent();
            _loggedInUser = loggedInUser;

            UserNameTextBox.Text = _loggedInUser.Username;
            UserNameTextBox.IsReadOnly = true;

            LoadTodayQuestion();
        }

        private void LoadTodayQuestion()
        {
            using var db = new QuizDbContext();

            todayQuestion = db.Questions.FirstOrDefault(q => q.ActiveDate == DateTime.Today);

            if (todayQuestion == null)
            {
                QuestionTextBlock.Text = "Ma nincs elérhető kérdés.";
                ShowResult("Ma nincs elérhető kérdés.", false);
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

            try
            {
                using var db = new QuizDbContext();

                bool alreadyAnswered = db.UserAnswers.Any(a =>
                    a.UserId == _loggedInUser.Id &&
                    a.AnswerDate.Date == DateTime.Today);

                if (alreadyAnswered)
                {
                    ShowNeutral("Ma már válaszoltál.");
                    DisableAnswerButtons();
                    return;
                }

                var clickedButton = sender as Button;
                string selectedAnswer = clickedButton?.Content?.ToString() ?? "";
                bool isCorrect = selectedAnswer == todayQuestion.CorrectAnswer;

                var userAnswer = new UserAnswer
                {
                    UserId = _loggedInUser.Id,
                    QuestionId = todayQuestion.Id,
                    SelectedAnswer = selectedAnswer,
                    AnswerDate = DateTime.Today,
                    IsCorrect = isCorrect
                };

                db.UserAnswers.Add(userAnswer);

                var user = db.Users.FirstOrDefault(u => u.Id == _loggedInUser.Id);

                if (user != null)
                {
                    if (isCorrect)
                    {
                        user.CurrentStreak++;

                        if (user.CurrentStreak > user.BestStreak)
                        {
                            user.BestStreak = user.CurrentStreak;
                        }

                        _loggedInUser.CurrentStreak = user.CurrentStreak;
                        _loggedInUser.BestStreak = user.BestStreak;
                    }
                    else
                    {
                        user.CurrentStreak = 0;
                        _loggedInUser.CurrentStreak = 0;
                    }
                }

                db.SaveChanges();

                HighlightSelectedButton(clickedButton, isCorrect);
                DisableOtherButtons(clickedButton);

                if (isCorrect)
                {
                    if (_loggedInUser.CurrentStreak >= 3)
                    {
                        ShowResult($"Helyes válasz! Streak: 🔥 {_loggedInUser.CurrentStreak}", true);
                    }
                    else
                    {
                        ShowResult("Helyes válasz!", true);
                    }
                }
                else
                {
                    ShowResult($"Helytelen válasz. A helyes válasz: {todayQuestion.CorrectAnswer}", false);
                }
            }
            catch (Exception ex)
            {
                ShowNeutral($"Hiba történt: {ex.Message}");
            }
        }

        private void DisableAnswerButtons()
        {
            ButtonA.IsEnabled = false;
            ButtonB.IsEnabled = false;
            ButtonC.IsEnabled = false;
            ButtonD.IsEnabled = false;

            ButtonA.Opacity = 0.9;
            ButtonB.Opacity = 0.9;
            ButtonC.Opacity = 0.9;
            ButtonD.Opacity = 0.9;

            ButtonA.Background = new SolidColorBrush((Color)ColorConverter.ConvertFromString("#A0A0A0"));
            ButtonB.Background = new SolidColorBrush((Color)ColorConverter.ConvertFromString("#A0A0A0"));
            ButtonC.Background = new SolidColorBrush((Color)ColorConverter.ConvertFromString("#A0A0A0"));
            ButtonD.Background = new SolidColorBrush((Color)ColorConverter.ConvertFromString("#A0A0A0"));
        }

        private void DisableOtherButtons(Button? clickedButton)
        {
            Button[] buttons = { ButtonA, ButtonB, ButtonC, ButtonD };

            foreach (var button in buttons)
            {
                button.IsEnabled = false;
                button.Opacity = 0.9;

                if (button != clickedButton)
                {
                    button.Background = new SolidColorBrush((Color)ColorConverter.ConvertFromString("#A0A0A0"));
                }
            }
        }

        private void HighlightSelectedButton(Button? clickedButton, bool isCorrect)
        {
            if (clickedButton == null)
                return;

            if (isCorrect)
            {
                clickedButton.Background = new SolidColorBrush((Color)ColorConverter.ConvertFromString("#16A34A"));
            }
            else
            {
                clickedButton.Background = new SolidColorBrush((Color)ColorConverter.ConvertFromString("#DC2626"));
            }
        }

        private void ShowResult(string message, bool success)
        {
            ResultBorder.Visibility = Visibility.Visible;
            ResultTextBlock.Text = message;

            if (success)
            {
                ResultBorder.Background = new SolidColorBrush((Color)ColorConverter.ConvertFromString("#DCFCE7"));
                ResultTextBlock.Foreground = new SolidColorBrush((Color)ColorConverter.ConvertFromString("#166534"));
            }
            else
            {
                ResultBorder.Background = new SolidColorBrush((Color)ColorConverter.ConvertFromString("#FEE2E2"));
                ResultTextBlock.Foreground = new SolidColorBrush((Color)ColorConverter.ConvertFromString("#991B1B"));
            }
        }

        private void ShowNeutral(string message)
        {
            ResultBorder.Visibility = Visibility.Visible;
            ResultTextBlock.Text = message;
            ResultBorder.Background = new SolidColorBrush((Color)ColorConverter.ConvertFromString("#E5E7EB"));
            ResultTextBlock.Foreground = new SolidColorBrush((Color)ColorConverter.ConvertFromString("#111827"));
        }

        private void BackButton_Click(object sender, RoutedEventArgs e)
        {
            Close();
        }
    }
}