using System.Net.Http;
using System.Text;
using System.Text.Json;
using NapiQuiz.Models;

namespace NapiQuiz.Services
{
    public class ApiService
    {
        private const string BaseUrl = "https://draftn-fizz.local.pepa.hu/api/";

        private static readonly JsonSerializerOptions JsonOptions = new()
        {
            PropertyNameCaseInsensitive = true
        };

        private HttpClient CreateClient()
        {
            var handler = new HttpClientHandler
            {
                ServerCertificateCustomValidationCallback =
                    HttpClientHandler.DangerousAcceptAnyServerCertificateValidator
            };

            return new HttpClient(handler);
        }

        public async Task<LoginResponse?> LoginAsync(string username, string password)
        {
            using var client = CreateClient();

            var payload = new
            {
                username,
                password
            };

            var json = JsonSerializer.Serialize(payload);
            var content = new StringContent(json, Encoding.UTF8, "application/json");

            var response = await client.PostAsync(BaseUrl + "login.php", content);
            var responseText = await response.Content.ReadAsStringAsync();

            return JsonSerializer.Deserialize<LoginResponse>(responseText, JsonOptions);
        }

        public async Task<QuestionResponse?> GetTodayQuestionAsync()
        {
            using var client = CreateClient();

            string json = await client.GetStringAsync(BaseUrl + "get_today_question.php");
            return JsonSerializer.Deserialize<QuestionResponse>(json, JsonOptions);
        }

        public async Task<SubmitAnswerResponse?> SubmitAnswerAsync(int userId, int questionId, string selectedAnswer)
        {
            using var client = CreateClient();

            var payload = new
            {
                user_id = userId,
                question_id = questionId,
                selected_answer = selectedAnswer
            };

            var json = JsonSerializer.Serialize(payload);
            var content = new StringContent(json, Encoding.UTF8, "application/json");

            var response = await client.PostAsync(BaseUrl + "submit_answer.php", content);
            var responseText = await response.Content.ReadAsStringAsync();

            return JsonSerializer.Deserialize<SubmitAnswerResponse>(responseText, JsonOptions);
        }

        public async Task<LeaderboardResponse?> GetLeaderboardAsync()
        {
            using var client = CreateClient();

            string json = await client.GetStringAsync(BaseUrl + "get_leaderboard.php");
            return JsonSerializer.Deserialize<LeaderboardResponse>(json, JsonOptions);
        }

        public async Task<bool> CreateQuestionAsync(string text, string answerA, string answerB, string answerC, string answerD, string correctAnswer)
        {
            using var client = CreateClient();

            var payload = new
            {
                text = text,
                answer_a = answerA,
                answer_b = answerB,
                answer_c = answerC,
                answer_d = answerD,
                correct_answer = correctAnswer
            };

            var json = JsonSerializer.Serialize(payload);
            var content = new StringContent(json, Encoding.UTF8, "application/json");

            var response = await client.PostAsync(BaseUrl + "create_question.php", content);
            var responseText = await response.Content.ReadAsStringAsync();

            var result = JsonSerializer.Deserialize<ApiResponse<object>>(responseText, JsonOptions);

            return result != null && result.success;
        }

        public async Task<bool> SetTodayQuestionAsync()
        {
            using var client = CreateClient();

            var response = await client.GetAsync(BaseUrl + "set_today_question.php");
            var responseText = await response.Content.ReadAsStringAsync();

            var result = JsonSerializer.Deserialize<ApiResponse<object>>(responseText, JsonOptions);

            return result != null && result.success;
        }
    }
}