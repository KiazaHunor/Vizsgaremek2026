using System;
using System.Windows;
using NapiQuiz.Models;
using NapiQuiz.Views;

namespace NapiQuiz
{
    public partial class MainWindow : Window
    {
        private User _loggedInUser;

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
                userWindow.Owner = this;
                userWindow.ShowDialog();
            }
            catch (Exception ex)
            {
                MessageBox.Show("Hiba a felhasználói ablak megnyitásakor:\n" + ex.Message);
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
                adminLoginWindow.Owner = this;
                adminLoginWindow.ShowDialog();
            }
            catch (Exception ex)
            {
                MessageBox.Show("Hiba az admin ablak megnyitásakor:\n" + ex.Message);
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
                leaderboardWindow.Owner = this;
                leaderboardWindow.ShowDialog();
            }
            catch (Exception ex)
            {
                MessageBox.Show("Hiba a toplista megnyitásakor:\n" + ex.Message);
            }
            finally
            {
                this.Show();
            }
        }

        private void LogoutButton_Click(object sender, RoutedEventArgs e)
        {
            var loginWindow = new LoginWindow();

            if (loginWindow.ShowDialog() == true)
            {
                _loggedInUser = loginWindow.LoggedInUser!;
            }
            else
            {
                Application.Current.Shutdown();
            }
        }
        
    }
}