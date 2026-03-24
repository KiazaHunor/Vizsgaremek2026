namespace FizzLigaQuiz.Models
{
    public class QuizValasz
    {
        public int Id { get; set; }
        public string? FelhasznaloNev { get; set; }
        public int KerdesId { get; set; }
        public string ValasztottValasz { get; set; } = "";
        public bool Helyes { get; set; }
        public DateTime ValaszDatuma { get; set; } = DateTime.Now;
    }
}
