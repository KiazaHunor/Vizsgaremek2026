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
            string connectionString = "server=localhost;database=fizzliga_db;user=root;password=;";

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