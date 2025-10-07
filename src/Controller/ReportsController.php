<?php

namespace App\Controller;

use App\Entity\Enums\UserRole;
use App\Entity\User;
use App\Entity\UserProgress;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
#[Route('/reports')]
class ReportsController extends AbstractController {
    #[Route('/users/spreadsheet', 'app_users_spreadsheet')]
    #[IsGranted('ROLE_ADMIN')]
    public function userSpreadSheet(EntityManagerInterface $em) {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Email');
        $sheet->setCellValue('C1', 'Ime');
        $sheet->setCellValue('D1', 'Prezime');
        $sheet->setCellValue('E1', 'Visina');
        $sheet->setCellValue('F1', 'Pozicija');
        $red = 2;
        $users = $em
            ->getRepository(User::class)
            ->findAll();
        foreach($users as $user) {
        $sheet->setCellValue('A'.$red, $user->getId());
        $sheet->setCellValue('B'.$red, $user->getEmail());
        $sheet->setCellValue('C'.$red, $user->getName());
        $sheet->setCellValue('D'.$red, $user->getSurname());
        $sheet->setCellValue('E'.$red, $user->getHeight());
        $sheet->setCellValue('F'.$red, $user->getRole()->value);
        $red++;
        }
        $writer = new Xlsx($spreadsheet);
        //Moramo da posaljemo browseru streamed response kako bi se generisao fajl
        $response = new StreamedResponse(function () use ($writer) {
            // Funkcija sluzi za generisanje output fajla
            $writer->save('php://output');
        });

        $filename = sprintf('izvestaj_korisnika.xlsx', date('d-m-Y'));
        //Podesavanje headera kako bi browser znao da treba skinuti fajl\
        //Definisemo tip podatka koji zahteva da browser skine
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        //Content-Disposition govori browseru da umesto prikazuje fajl da ga skine
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $filename . '"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }
    #[Route('/user/{id}/spreadsheet', 'app_users_spreadsheet')]
    #[IsGranted('ROLE_ADMIN')]
    public function userProgressSpreadSheet(User $user, EntityManagerInterface $em) {
        if($user->getRole() !== UserRole::USER) {
            $this->addFlash('warning', 'Izvesta koji ste probali izvuci ne pripada klijentu!');
            return $this->redirectToRoute('app_user_profile', ['id' => $user->getId()]);
        }
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Email');
        $sheet->setCellValue('C1', 'Ime');
        $sheet->setCellValue('D1', 'Prezime');
        $sheet->setCellValue('E1', 'Visina');
        $sheet->setCellValue('F1', 'Kilaza');
        $sheet->setCellValue('G1', 'Datum');
        $red = 2;
        $usersProgress = $em
            ->getRepository(UserProgress::class)
            ->findBy(
                ['user' => $user],
                ['date' => 'DESC']
            );
        foreach($usersProgress as $up) {
        $sheet->setCellValue('A'.$red, $user->getId());
        $sheet->setCellValue('B'.$red, $user->getEmail());
        $sheet->setCellValue('C'.$red, $user->getName());
        $sheet->setCellValue('D'.$red, $user->getSurname());
        $sheet->setCellValue('E'.$red, $user->getHeight());
        $sheet->setCellValue('F'.$red, $up->getWeightKg());
        $sheet->setCellValue('G'.$red, $up->getDate());
        $red++;
        }
        $writer = new Xlsx($spreadsheet);
        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });

        $filename = sprintf('izvestaj_korisnickog_napredka.xlsx', date('d-m-Y'));
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $filename . '"');
        $response->headers->set('Cache-Control', 'max-age=0');
        return $response;
    }
}

?>