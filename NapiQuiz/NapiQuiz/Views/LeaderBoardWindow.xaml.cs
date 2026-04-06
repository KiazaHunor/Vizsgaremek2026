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

            var top = db.UserAnswers
                .Where(a => a.IsCorrect)
                .Join(db.Users,
                      answer => answer.UserId,
                      user => user.Id,
                      (answer, user) => new { answer, user })
                .GroupBy(x => new { x.user.Id, x.user.Username })
                .Select(g => new
                {
                    Username = g.Key.Username,
                    Score = g.Count()
                })
                .OrderByDescending(x => x.Score)
                .ToList();

            LeaderboardListBox.Items.Clear();

            int rank = 1;

            foreach (var item in top)
            {
                LeaderboardListBox.Items.Add($"{rank}. {item.Username} - {item.Score} pont");
                rank++;
            }
        }
        private void BackButton_Click(object sender, RoutedEventArgs e)
        {
            this.Close();
        }
    }
}