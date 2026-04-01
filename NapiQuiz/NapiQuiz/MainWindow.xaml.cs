using NapiQuiz.Views;
using System.Text;
using System.Windows;
using System.Windows.Controls;
using System.Windows.Data;
using System.Windows.Documents;
using System.Windows.Input;
using System.Windows.Media;
using System.Windows.Media.Imaging;
using System.Windows.Navigation;
using System.Windows.Shapes;

namespace NapiQuiz
{
    public partial class MainWindow : Window
    {
        public MainWindow()
        {
            InitializeComponent();
        }

        private void UserButton_Click(object sender, RoutedEventArgs e)
        {
            new UserWindow().ShowDialog();
        }

        private void AdminButton_Click(object sender, RoutedEventArgs e)
        {
            new AdminLoginWindow().ShowDialog();
        }
        private void LeaderboardButton_Click(object sender, RoutedEventArgs e)
        {
            new LeaderboardWindow().ShowDialog();
        }
    }
}