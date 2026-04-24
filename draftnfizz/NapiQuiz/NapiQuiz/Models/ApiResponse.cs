namespace NapiQuiz.Models
{
    public class ApiResponse<T>
    {
        public bool success { get; set; }
        public string? error { get; set; }
        public T? data { get; set; }
    }
}