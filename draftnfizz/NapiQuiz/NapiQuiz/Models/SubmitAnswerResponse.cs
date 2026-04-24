namespace NapiQuiz.Models
{
    public class SubmitAnswerResponse
    {
        public bool success { get; set; }
        public string? error { get; set; }
        public bool is_correct { get; set; }
        public string? correct_answer { get; set; }
        public int current_streak { get; set; }
        public int best_streak { get; set; }
        public bool already_answered { get; set; }
    }
}