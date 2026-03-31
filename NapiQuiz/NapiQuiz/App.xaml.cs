using NapiQuiz.Data;
using System.Configuration;
using System.Data;
using System.Windows;

namespace NapiQuiz
{
    /// <summary>
    /// Interaction logic for App.xaml
    /// </summary>
    public partial class App : Application
    {
        protected override void OnStartup(StartupEventArgs e)
        {
            using var db = new QuizDbContext();
            db.Database.EnsureCreated();

            base.OnStartup(e);

        }

    }
}