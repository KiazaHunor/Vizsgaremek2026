using System.Linq;
using System.Windows;
using NapiQuiz.Data;

namespace NapiQuiz.Views
{
    public partial class LeaderboardWindow : Window
    {
        public LeaderboardWindow()
        {
            InitializeComponent();
            LoadLeaderboard();
        }

        private void LoadLeaderboard()
        {
            using var db = new QuizDbContext();

            var top = db.Users
                .Select(user => new
                {
                    Username = user.Username,
                    Score = db.UserAnswers.Count(a => a.UserId == user.Id && a.IsCorrect),
                    CurrentStreak = user.CurrentStreak
                })
                .OrderByDescending(x => x.Score)
                .ThenBy(x => x.Username)
                .ToList();

            LeaderboardListBox.Items.Clear();

            int rank = 1;

            foreach (var item in top)
            {
                string rankDisplay = rank + ".";

                if (rank == 1)
                {
                    rankDisplay = "🥇";
                }
                else if (rank == 2)
                {
                    rankDisplay = "🥈";
                }
                else if (rank == 3)
                {
                    rankDisplay = "🥉";
                }

                string streakText = "";

                if (item.CurrentStreak >= 2)
                {
                    streakText = "  🔥 " + item.CurrentStreak;
                }

                LeaderboardListBox.Items.Add($"{rankDisplay} {item.Username} - {item.Score} kredit{streakText}");
                rank++;
            }
        }

        private void BackButton_Click(object sender, RoutedEventArgs e)
        {
            this.Close();
        }
    }
}