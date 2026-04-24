using System;
using System.Linq;
using System.Windows;
using System.Windows.Controls;
using NapiQuiz.Services;
using NapiQuiz.Models;
namespace NapiQuiz.Views
{
    public partial class AdminWindow : Window
    {
        private readonly ApiService _apiService = new ApiService();

        public AdminWindow()
        {
            InitializeComponent();
        }

        private async void SaveQuestion_Click(object sender, RoutedEventArgs e)
        {
            string questionText = QuestionTextBox.Text.Trim();
            string answerA = AnswerATextBox.Text.Trim();
            string answerB = AnswerBTextBox.Text.Trim();
            string answerC = AnswerCTextBox.Text.Trim();
            string answerD = AnswerDTextBox.Text.Trim();

            if (string.IsNullOrWhiteSpace(questionText) ||
                string.IsNullOrWhiteSpace(answerA) ||
                string.IsNullOrWhiteSpace(answerB) ||
                string.IsNullOrWhiteSpace(answerC) ||
                string.IsNullOrWhiteSpace(answerD) ||
                CorrectAnswerComboBox.SelectedItem == null)
            {
                MessageBox.Show("Minden mezőt tölts ki.");
                return;
            }

            var answers = new[] { answerA, answerB, answerC, answerD };
            if (answers.Distinct(StringComparer.OrdinalIgnoreCase).Count() < 4)
            {
                MessageBox.Show("Az A, B, C és D válaszok legyenek különbözőek.");
                return;
            }

            string selectedLetter = ((ComboBoxItem)CorrectAnswerComboBox.SelectedItem).Content.ToString()!;

            string correctAnswer = selectedLetter switch
            {
                "A" => answerA,
                "B" => answerB,
                "C" => answerC,
                "D" => answerD,
                _ => ""
            };

            try
            {
                bool success = await _apiService.CreateQuestionAsync(
                    questionText,
                    answerA,
                    answerB,
                    answerC,
                    answerD,
                    correctAnswer
                );

                if (!success)
                {
                    MessageBox.Show("Nem sikerült elmenteni a kérdést.");
                    return;
                }

                MessageBox.Show("Kérdés elmentve.");

                QuestionTextBox.Clear();
                AnswerATextBox.Clear();
                AnswerBTextBox.Clear();
                AnswerCTextBox.Clear();
                AnswerDTextBox.Clear();
                CorrectAnswerComboBox.SelectedIndex = -1;
            }
            catch (Exception ex)
            {
                MessageBox.Show("Hiba mentés közben:\n" + ex.Message);
            }
        }

        private async void SetTodayQuestion_Click(object sender, RoutedEventArgs e)
        {
            try
            {
                bool success = await _apiService.SetTodayQuestionAsync();

                if (!success)
                {
                    MessageBox.Show("Nem sikerült beállítani a mai kérdést.");
                    return;
                }

                MessageBox.Show("A legutolsó kérdés lett a mai kérdés.");
            }
            catch (Exception ex)
            {
                MessageBox.Show("Hiba a mai kérdés beállításakor:\n" + ex.Message);
            }
        }

        private void BackButton_Click(object sender, RoutedEventArgs e)
        {
            Close();
        }
    }
}