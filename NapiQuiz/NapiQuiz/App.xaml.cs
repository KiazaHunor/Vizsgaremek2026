using System.Windows;
using NapiQuiz.Views;

namespace NapiQuiz
{
    public partial class App : Application
    {
        protected override void OnStartup(StartupEventArgs e)
        {
            ShutdownMode = ShutdownMode.OnExplicitShutdown;
            base.OnStartup(e);

            var loginWindow = new LoginWindow();
            bool? result = loginWindow.ShowDialog();

            if (result == true && loginWindow.LoggedInUser != null)
            {
                var mainWindow = new MainWindow(loginWindow.LoggedInUser);
                MainWindow = mainWindow;

                ShutdownMode = ShutdownMode.OnMainWindowClose;
                mainWindow.Show();
            }
            else
            {
                Shutdown();
            }
        }
    }
}