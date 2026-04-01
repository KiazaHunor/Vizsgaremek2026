using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace NapiQuiz.Models
{
    public class UserAnswer
    {
        public int Id { get; set; }
        public string UserName { get; set; } = "";
        public int QuestionId { get; set; }
        public string SelectedAnswer { get; set; } = "";
        public DateTime AnswerDate { get; set; }
        public bool IsCorrect { get; set; }
    }
}
