import java.util.Scanner;

public class Main{
    public static void main(String[] agrs)
    {
        Scanner scanner = new Scanner(System.in);
        System.out.println("Enter cc number: ");
        int cNumber = scanner.nextInt();
        scanner.close();  
    }
}


/*class cc_verification
{
    cc_verification(int cNumber)
    {
        arr = [int(cNumber) for digit in str(cNumber)]
    }
} */


