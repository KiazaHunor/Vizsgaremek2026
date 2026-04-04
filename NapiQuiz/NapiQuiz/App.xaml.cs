using System.Windows;
using NapiQuiz.Views;

namespace NapiQuiz
{
    public partial class App : Application
    {
        protected override void OnStartup(StartupEventArgs e)
        {
            using (var db = new Data.QuizDbContext())
            {
                db.Database.EnsureCreated();
            }

            var loginWindow = new LoginWindow();
            bool? result = loginWindow.ShowDialog();

            if (result == true && loginWindow.LoggedInUser != null)
            {
                var mainWindow = new MainWindow(loginWindow.LoggedInUser);
                mainWindow.Show();
            }
            else
            {
                Shutdown();
            }

            base.OnStartup(e);
        }
    }
}