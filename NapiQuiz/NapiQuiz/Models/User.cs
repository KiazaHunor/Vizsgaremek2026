using System;
using System.ComponentModel.DataAnnotations.Schema;

namespace NapiQuiz.Models
{
    [Table("users")]
    public class User
    {
        [Column("id")]
        public int Id { get; set; }

        [Column("username")]
        public string Username { get; set; } = "";

        [Column("password")]
        public string Password { get; set; } = "";

        [Column("email")]
        public string Email { get; set; } = "";

        [Column("token")]
        public string? Token { get; set; }

        [Column("token_expiry")]
        public DateTime? TokenExpiry { get; set; }

        [Column("password_reset_token")]
        public string? PasswordResetToken { get; set; }

        [Column("password_reset_expiry")]
        public DateTime? PasswordResetExpiry { get; set; }

        [Column("created_at")]
        public DateTime CreatedAt { get; set; }

        [Column("email_token")]
        public string? EmailToken { get; set; }

        [Column("email_verified")]
        public bool EmailVerified { get; set; }

        [Column("profile_image")]
        public string? ProfileImage { get; set; }

        [Column("current_streak")]
        public int CurrentStreak { get; set; }

        [Column("best_streak")]
        public int BestStreak { get; set; }
    }
}