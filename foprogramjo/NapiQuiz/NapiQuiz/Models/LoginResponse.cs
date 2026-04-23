namespace NapiQuiz.Models
{
    public class LoginResponse
    {
        public bool success { get; set; }
        public string? error { get; set; }
        public User? user { get; set; }
    }
}