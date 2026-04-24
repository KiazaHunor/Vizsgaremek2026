using System.Windows;
using NapiQuiz.Services;
using NapiQuiz.Models;
namespace NapiQuiz.Views
{
    public partial class LeaderboardWindow : Window
    {
        private readonly ApiService _apiService = new ApiService();

        public LeaderboardWindow()
        {
            InitializeComponent();
            LoadLeaderboard();
        }

        private async void LoadLeaderboard()
        {
            try
            {
                var result = await _apiService.GetLeaderboardAsync();

                LeaderboardListBox.Items.Clear();

                if (result == null || !result.success)
                {
                    LeaderboardListBox.Items.Add("Nem sikerült betölteni a toplistát.");
                    return;
                }

                int rank = 1;

                foreach (var item in result.leaderboard)
                {
                    string rankDisplay = rank switch
                    {
                        1 => "🥇",
                        2 => "🥈",
                        3 => "🥉",
                        _ => rank + "."
                    };

                    string streakText = item.CurrentStreak >= 2 ? "  🔥 " + item.CurrentStreak : "";
                    LeaderboardListBox.Items.Add($"{rankDisplay} {item.Username} - {item.Score} kredit{streakText}");
                    rank++;
                }
            }
            catch (Exception ex)
            {
                LeaderboardListBox.Items.Clear();
                LeaderboardListBox.Items.Add("Hiba: " + ex.Message);
            }
        }

        private void BackButton_Click(object sender, RoutedEventArgs e)
        {
            Close();
        }
    }
}