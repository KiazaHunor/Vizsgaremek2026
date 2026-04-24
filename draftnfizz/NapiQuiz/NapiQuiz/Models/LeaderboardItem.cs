namespace NapiQuiz.Models
{
    public class LeaderboardItem
    {
        public string Username { get; set; } = "";
        public int Score { get; set; }
        public int CurrentStreak { get; set; }
    }
}