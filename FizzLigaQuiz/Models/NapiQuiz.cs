namespace FizzLigaQuiz.Models
{
    public class NapiQuiz
    {
        public int Id { get; set; }
        public DateTime Datum { get; set; }
        public int KerdesId { get; set; }

        public Kerdes? Kerdes { get; set; }
    }
}
