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
            if (string.IsNullOrWhiteSpace(QuestionTextBox.Text) ||
                string.IsNullOrWhiteSpace(AnswerATextBox.Text) ||
                string.IsNullOrWhiteSpace(AnswerBTextBox.Text) ||
                string.IsNullOrWhiteSpace(AnswerCTextBox.Text) ||
                string.IsNullOrWhiteSpace(AnswerDTextBox.Text) ||
                string.IsNullOrWhiteSpace(CorrectAnswerTextBox.Text))
            {
                MessageBox.Show("Minden mezőt tölts ki.");
                return;
            }

            using var db = new QuizDbContext();

            var question = new Question
            {
                Text = QuestionTextBox.Text,
                AnswerA = AnswerATextBox.Text,
                AnswerB = AnswerBTextBox.Text,
                AnswerC = AnswerCTextBox.Text,
                AnswerD = AnswerDTextBox.Text,
                CorrectAnswer = CorrectAnswerTextBox.Text
            };

            db.Questions.Add(question);
            db.SaveChanges();

            MessageBox.Show("Kérdés elmentve.");

            QuestionTextBox.Clear();
            AnswerATextBox.Clear();
            AnswerBTextBox.Clear();
            AnswerCTextBox.Clear();
            AnswerDTextBox.Clear();
            CorrectAnswerTextBox.Clear();
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
    }
}