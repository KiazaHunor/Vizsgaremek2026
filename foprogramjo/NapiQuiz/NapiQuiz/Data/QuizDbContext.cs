/*
using Microsoft.EntityFrameworkCore;
using NapiQuiz.Models;

namespace NapiQuiz.Data
{
    public class QuizDbContext : DbContext
    {
        public DbSet<Question> Questions { get; set; }
        public DbSet<UserAnswer> UserAnswers { get; set; }
        public DbSet<User> Users { get; set; }

        protected override void OnConfiguring(DbContextOptionsBuilder optionsBuilder)
        {
            string connectionString = "host=localhost;database=draftn-fizz_ady_pepa_hu;user=draftn-fizz_ady_pepa_hu_usr;password=QkRyx16QP4LnhEYCkGEUEg;";

            optionsBuilder.UseMySql(
                connectionString,
                ServerVersion.AutoDetect(connectionString)
            );
        }
        protected override void OnModelCreating(ModelBuilder modelBuilder)
        {
            modelBuilder.Entity<User>().ToTable("users");
            modelBuilder.Entity<Question>().ToTable("questions");
            modelBuilder.Entity<UserAnswer>().ToTable("user_answers");
        }
    }
}
*/