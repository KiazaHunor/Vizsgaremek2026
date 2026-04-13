using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Windows;
using System.Windows.Controls;
using System.Windows.Data;
using System.Windows.Documents;
using System.Windows.Input;
using System.Windows.Media;
using System.Windows.Media.Imaging;
using System.Windows.Shapes;
using NapiQuiz.Data;
using NapiQuiz.Models;

namespace NapiQuiz.Views
{
    public partial class AdminWindow : Window
    {
        public AdminWindow()
        {
            InitializeComponent();
        }

        private void SaveQuestion_Click(object sender, RoutedEventArgs e)
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

            using var db = new QuizDbContext();

            var question = new Question
            {
                Text = questionText,
                AnswerA = answerA,
                AnswerB = answerB,
                AnswerC = answerC,
                AnswerD = answerD,
                CorrectAnswer = correctAnswer
            };

            db.Questions.Add(question);
            db.SaveChanges();

            MessageBox.Show("Kérdés elmentve.");

            QuestionTextBox.Clear();
            AnswerATextBox.Clear();
            AnswerBTextBox.Clear();
            AnswerCTextBox.Clear();
            AnswerDTextBox.Clear();
            CorrectAnswerComboBox.SelectedIndex = -1;
        }



        private void SetTodayQuestion_Click(object sender, RoutedEventArgs e)
        {
            using var db = new QuizDbContext();

            var oldTodayQuestions = db.Questions
                .Where(q => q.ActiveDate == DateTime.Today)
                .ToList();

            foreach (var item in oldTodayQuestions)
            {
                item.ActiveDate = null;
            }

            var latestQuestion = db.Questions
                .OrderByDescending(q => q.Id)
                .FirstOrDefault();

            if (latestQuestion == null)
            {
                MessageBox.Show("Nincs még elmentett kérdés.");
                return;
            }

            latestQuestion.ActiveDate = DateTime.Today;
            db.SaveChanges();

            MessageBox.Show("A legutolsó kérdés lett a mai kérdés.");
        }
        private void BackButton_Click(object sender, RoutedEventArgs e)
        {
            this.Close();
        }
    }
}