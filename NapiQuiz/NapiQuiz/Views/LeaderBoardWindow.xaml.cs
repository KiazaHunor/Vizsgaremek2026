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
                .GroupBy(a => a.UserId)
                .Select(g => new
                {
                    User = g.Key,
                    Score = g.Count()
                })
                .OrderByDescending(x => x.Score)
                .ToList();

            LeaderboardListBox.Items.Clear();

            foreach (var item in top)
            {
                LeaderboardListBox.Items.Add($"{item.User} - {item.Score} pont");
            }
        }
    }
}