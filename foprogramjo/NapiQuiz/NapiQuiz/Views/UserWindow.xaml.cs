using System.Windows;
using System.Windows.Controls;
using System.Windows.Media;
using NapiQuiz.Services;
using NapiQuiz.Models;
namespace NapiQuiz.Views
{
    public partial class UserWindow : Window
    {
        private readonly User _loggedInUser;
        private readonly ApiService _apiService = new ApiService();
        private Question? todayQuestion;

        public UserWindow(User loggedInUser)
        {
            InitializeComponent();
            _loggedInUser = loggedInUser;

            UserNameTextBox.Text = _loggedInUser.Username;
            UserNameTextBox.IsReadOnly = true;

            LoadTodayQuestion();
        }

        private async void LoadTodayQuestion()
        {
            try
            {
                var result = await _apiService.GetTodayQuestionAsync();

                if (result == null || !result.success || result.question == null)
                {
                    QuestionTextBlock.Text = "Ma nincs elérhető kérdés.";
                    ShowResult("Ma nincs elérhető kérdés.", false);
                    DisableAnswerButtons();
                    return;
                }

                todayQuestion = result.question;

                QuestionTextBlock.Text = todayQuestion.Text;
                ButtonA.Content = todayQuestion.AnswerA;
                ButtonB.Content = todayQuestion.AnswerB;
                ButtonC.Content = todayQuestion.AnswerC;
                ButtonD.Content = todayQuestion.AnswerD;
            }
            catch (Exception ex)
            {
                ShowNeutral("Hiba történt: " + ex.Message);
            }
        }

        private async void Answer_Click(object sender, RoutedEventArgs e)
        {
            if (todayQuestion == null)
                return;

            try
            {
                var clickedButton = sender as Button;
                string selectedAnswer = clickedButton?.Content?.ToString() ?? "";

                var result = await _apiService.SubmitAnswerAsync(_loggedInUser.Id, todayQuestion.Id, selectedAnswer);

                if (result == null)
                {
                    ShowNeutral("Nem érkezett válasz a szervertől.");
                    return;
                }

                if (result.already_answered)
                {
                    ShowNeutral("Ma már válaszoltál.");
                    DisableAnswerButtons();
                    return;
                }

                if (!result.success)
                {
                    ShowNeutral(result.error ?? "Hiba történt.");
                    return;
                }

                _loggedInUser.CurrentStreak = result.current_streak;
                _loggedInUser.BestStreak = result.best_streak;

                HighlightSelectedButton(clickedButton, result.is_correct);
                DisableOtherButtons(clickedButton);

                if (result.is_correct)
                {
                    if (_loggedInUser.CurrentStreak >= 3)
                        ShowResult($"Helyes válasz! Streak: 🔥 {_loggedInUser.CurrentStreak}", true);
                    else
                        ShowResult("Helyes válasz!", true);
                }
                else
                {
                    ShowResult($"Helytelen válasz. A helyes válasz: {result.correct_answer}", false);
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
                    button.Background = new SolidColorBrush((Color)ColorConverter.ConvertFromString("#A0A0A0"));
            }
        }

        private void HighlightSelectedButton(Button? clickedButton, bool isCorrect)
        {
            if (clickedButton == null)
                return;

            clickedButton.Background = isCorrect
                ? new SolidColorBrush((Color)ColorConverter.ConvertFromString("#16A34A"))
                : new SolidColorBrush((Color)ColorConverter.ConvertFromString("#DC2626"));
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