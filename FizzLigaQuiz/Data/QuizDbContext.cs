using FizzLigaQuiz.Models;
using System.Collections.Generic;
using System.Reflection.Emit;
using Microsoft.EntityFrameworkCore;
namespace FizzLigaQuiz.Data
{
    public class QuizDbContext : DbContext
    {
        public QuizDbContext(DbContextOptions<QuizDbContext> options) : base(options)
        {
        }

        public DbSet<Kerdes> Kerdesek { get; set; }
        public DbSet<NapiQuiz> NapiQuizok { get; set; }
        public DbSet<QuizValasz> QuizValaszok { get; set; }

        protected override void OnModelCreating(ModelBuilder modelBuilder)
        {
            modelBuilder.Entity<NapiQuiz>()
                .HasOne(n => n.Kerdes)
                .WithMany()
                .HasForeignKey(n => n.KerdesId);

            modelBuilder.Entity<NapiQuiz>()
                .HasIndex(n => n.Datum)
                .IsUnique();
        }
    }
}
