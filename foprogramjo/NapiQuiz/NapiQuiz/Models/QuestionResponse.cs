namespace NapiQuiz.Models
{
    public class QuestionResponse
    {
        public bool success { get; set; }
        public string? error { get; set; }
        public Question? question { get; set; }
    }
}