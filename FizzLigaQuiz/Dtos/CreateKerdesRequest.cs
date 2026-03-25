namespace FizzLigaQuiz.Dtos
{
    public class CreateKerdesRequest
    {

        
        public string KerdesSzoveg { get; set; } = "";
        public string ValaszA { get; set; } = "";
        public string ValaszB { get; set; } = "";
        public string ValaszC { get; set; } = "";
        public string ValaszD { get; set; } = "";
        public string HelyesValasz { get; set; } = "";
        public string? Kategoria { get; set; }
        public string? Nehezseg { get; set; }
    }
}
