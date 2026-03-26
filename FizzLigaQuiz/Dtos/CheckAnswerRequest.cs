namespace FizzLigaQuiz.Dtos
{
    public class CheckAnswerRequest
    {
        public int KerdesId { get; set; }
        public string ValasztottValasz { get; set; } = "";
        public string? FelhasznaloNev { get; set; }
    }
}
