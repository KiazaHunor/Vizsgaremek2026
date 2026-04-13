using System;
using System.ComponentModel.DataAnnotations.Schema;

namespace NapiQuiz.Models
{
    [Table("user_answers")]
    public class UserAnswer
    {
        [Column("id")]
        public int Id { get; set; }

        [Column("user_id")]
        public int UserId { get; set; }

        [Column("question_id")]
        public int QuestionId { get; set; }

        [Column("selected_answer")]
        public string SelectedAnswer { get; set; } = "";

        [Column("answer_date")]
        public DateTime AnswerDate { get; set; }

        [Column("is_correct")]
        public bool IsCorrect { get; set; }
    }
}