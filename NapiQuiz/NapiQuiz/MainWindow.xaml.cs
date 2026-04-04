using System.Windows;
using NapiQuiz.Models;
using NapiQuiz.Views;

namespace NapiQuiz
{
    public partial class MainWindow : Window
    {
        private readonly User _loggedInUser;

        public MainWindow(User loggedInUser)
        {
            InitializeComponent();
            _loggedInUser = loggedInUser;
        }

        private void UserButton_Click(object sender, RoutedEventArgs e)
        {
            try
            {
                this.Hide();

                var userWindow = new UserWindow(_loggedInUser);
                userWindow.ShowDialog();
            }
            finally
            {
                this.Show();
            }
        }

        private void AdminButton_Click(object sender, RoutedEventArgs e)
        {
            try
            {
                this.Hide();

                var adminLoginWindow = new AdminLoginWindow();
                adminLoginWindow.ShowDialog();
            }
            finally
            {
                this.Show();
            }
        }

        private void LeaderboardButton_Click(object sender, RoutedEventArgs e)
        {
            try
            {
                this.Hide();

                var leaderboardWindow = new LeaderboardWindow();
                leaderboardWindow.ShowDialog();
            }
            finally
            {
                this.Show();
            }
        }
    }
}