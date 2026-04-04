using System;
using System.ComponentModel.DataAnnotations.Schema;

namespace NapiQuiz.Models
{
    [Table("questions")]
    public class Question
    {
        [Column("id")]
        public int Id { get; set; }

        [Column("text")]
        public string Text { get; set; } = "";

        [Column("answer_a")]
        public string AnswerA { get; set; } = "";

        [Column("answer_b")]
        public string AnswerB { get; set; } = "";

        [Column("answer_c")]
        public string AnswerC { get; set; } = "";

        [Column("answer_d")]
        public string AnswerD { get; set; } = "";

        [Column("correct_answer")]
        public string CorrectAnswer { get; set; } = "";

        [Column("active_date")]
        public DateTime? ActiveDate { get; set; }
    }
}