namespace NapiQuiz.Models
{
	public class LeaderboardResponse
	{
		public bool success { get; set; }
		public string? error { get; set; }
		public List<LeaderboardItem> leaderboard { get; set; } = new();
	}
}